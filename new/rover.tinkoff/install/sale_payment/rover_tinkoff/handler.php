<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Request;
use Bitrix\Main\Loader;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Sale;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;
use Bitrix\Sale\Order;
use Rover\Tinkoff\Dependence;
use Rover\Tinkoff\Log;
use Rover\Tinkoff\Status;
use \Rover\Tinkoff\Request as RequestTinkoff;

Loc::loadMessages(__FILE__);


if (!Loader::includeModule('rover.tinkoff'))
    throw new SystemException('rover.tinkoff module not found');
/**
 * Class rover_tinkoffHandler
 *
 * @package Sale\Handlers\PaySystem
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class rover_tinkoffHandler extends PaySystem\ServiceHandler implements PaySystem\IRefund,  PaySystem\ICheckable
{
    /**
     * order params cache
     * @var array
     */
    protected $params;

    /**
     * current payment status
     * @var string
     */
    protected $paymentStatus;

    /**
     * current payment id
     * @var int
     */
    protected $paymentId;

    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
        $params         = $this->getParams($payment);
        $dependence     = new Dependence();

        if ($dependence->checkBase()->getResult()) {
            $extraParams    = \RoverTinkoffHelper::getExtraParams($this, $params, $payment);
        } else {
            $errors                         = $dependence->getErrors();
            $extraParams = array(
                'READY_TO_PAY'    => false,
                'MESSAGE'         => implode('<br>', $errors)
            );
        }

        $this->setExtraParams($extraParams);

        return $this->showTemplate($payment, "template");
    }

    /**
     * @param Payment $payment
     * @return array
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function getParams(Sale\Payment $payment)
    {
        if (!is_array($this->params))
        {
            $params = $this->getParamsBusValue($payment);
            foreach ($params as $key => $value)
                $this->params[$key] = trim($value);
        }

        return $this->params;
    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        return array('RUB');
    }

    /**
     * @param Payment $payment
     * @param         $summ
     * @return PaySystem\ServiceResult
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function refund(Sale\Payment $payment, $summ)
    {
        $result = new PaySystem\ServiceResult();
        $params = $this->getParams($payment);

        if (!isset($params['ORDER_ID'])){
            $result->addError(new Error(Loc::getMessage('rover-tp__order-id-not-found-error')));
            Log::error('refund: ' . Loc::getMessage('rover-tp__order-id-not-found-error'));

            return $result;
        }

        try{
            /** @var $order Order */
            $order      = Order::loadByAccountNumber($params['ORDER_ID']);
            $arOrder    = $order->getFieldValues();

            if (!$arOrder) {
                $result->addError(new Error(Loc::getMessage('rover-tp__order-not-found-error')));
                Log::error('refund: ' . Loc::getMessage('rover-tp__order-not-found-error'));

                return $result;
            }

            if (empty($arOrder['PAY_VOUCHER_NUM'])) {
                $result->addError(new Error(Loc::getMessage('rover-tp__payment-id-not-found-error')));
                Log::error('refund: ' . Loc::getMessage('rover-tp__payment-id-not-found-error'));

                return $result;
            }

            $requestParams = \RoverTinkoffHelper::getRefundParams($params, $arOrder);

            RequestTinkoff::refund($requestParams);
            $result->setOperationType(PaySystem\ServiceResult::MONEY_LEAVING);
        } catch (\Exception $e) {
            $result->addError(new Error($e->getMessage()));
            Log::error('refund: ' . $e);
        }

        return $result;
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function processRequest(Payment $payment, Request $request)
    {
        die('process request');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        die('getPaymentIdFromRequest');
    }

    /**
     * @param Payment $payment
     * @return array
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function initPayment(Sale\Payment $payment)
    {
        $params = $this->getParams($payment);

        if (empty($params['TERMINAL_ID'])
            || empty($params['ORDER_ID'])
            || empty($params['SHOP_SECRET_WORD']))
            throw new SystemException('required params not found');

        $requestParams  = \RoverTinkoffHelper::getInitParams($payment, $params);
        $requestResult  = RequestTinkoff::init($requestParams);

        $paymentUrl = parse_url($requestResult['PaymentURL']);
        $urlParams  = array();

        parse_str($paymentUrl['query'], $urlParams);

        $this->paymentStatus    = $requestResult['Status'];
        $this->paymentId        = $requestResult['PaymentId'];

        return array(
            'url'       => $requestResult['PaymentURL'],
            'params'    => $urlParams,
        );
    }

    /**
     * @param Payment $payment
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function check(Sale\Payment $payment)
    {
        try{
            $request = (array)json_decode(file_get_contents("php://input"));

            /** @var $order Order */
            $order      = Order::loadByAccountNumber($request['OrderId']);
            if (!$order instanceof Order)
                throw new SystemException('order not found');

            $orderID = $order->getId();

            $this->paymentStatus    = $request['Status'];
            $this->paymentId        = $request['PaymentId'];

            $saleOrder = new \CSaleOrder();

            // failed
            if (Status::isOrderFailed($this)) {

                $saleOrder->PayOrder($orderID, 'N');
                Log::note('order #' . $orderID .' failed');

            // paid
            } elseif (Status::isOrderPaid($this)) {
                $saleOrder->PayOrder($orderID, 'Y', true, true, 0, array(
                    'PAY_VOUCHER_NUM'   => $this->paymentId,
                    'PAY_VOUCHER_DATE'  => new \Bitrix\Main\Type\Date(),
                ));

                // payment update
                $payment->setPaid('Y');
                $payment->setField('PAY_VOUCHER_NUM', $this->paymentId);
                $payment->save();

                $params = $this->getParams($payment);
                if (isset($params['ORDER_STATUS_PAYED']))
                    $payedStatus = $params['ORDER_STATUS_PAYED'];

                if (empty($payedStatus)) $payedStatus = 'P';

                $saleOrder->StatusOrder($orderID, $payedStatus);

                Log::note('order #' . $orderID .' payed, payment id: ' . $this->paymentId);

            // refund
            } elseif (Status::isOrderRefunded($this)) {

                $params = $this->getParams($payment);

                $saleOrder->PayOrder($orderID, 'N');

                if (isset($params['CANCEL_REFUNDED_ORDER'])
                    && ($params['CANCEL_REFUNDED_ORDER'] == 'Y'))
                    $saleOrder->CancelOrder($orderID, "Y", Loc::getMessage('SALE_TINKOFF_REFUNDED_DESCR'));

                if (isset($params['ORDER_STATUS_REFUNDED']))
                    $refundStatus = $params['ORDER_STATUS_REFUNDED'];

                if (empty($refundStatus)) $refundStatus = 'N';
                $saleOrder->StatusOrder($orderID, $refundStatus);

                Log::note('order #' . $orderID . ' refunded');

            // authorized
            } elseif (Status::isAuthorized($this)) {
                $params = $this->getParams($payment);
                if (isset($params['ORDER_STATUS_AUTHORIZED']))
                    $saleOrder->StatusOrder($orderID, $params['ORDER_STATUS_AUTHORIZED']);

                if (isset($params['ORDER_AUTHORIZED_FLAG_PAYED'])
                    && ($params['ORDER_AUTHORIZED_FLAG_PAYED'] == 'Y'))
                {
                    $saleOrder->PayOrder($orderID, 'Y', true, true, 0, array(
                        'PAY_VOUCHER_NUM'   => $this->paymentId,
                        'PAY_VOUCHER_DATE'  => new \Bitrix\Main\Type\Date(),
                    ));

                    // payment update
                    $payment->setPaid('Y');
                    $payment->setField('PAY_VOUCHER_NUM', $this->paymentId);
                    $payment->save();
                }

                Log::note('order #' . $orderID . ' authorized, paymentId: ' . $this->paymentId);

            // unknown
            } else {
                Log::note('order #' . $orderID .' unknown status: ' . $this->paymentStatus . ', paymentId: ' . $this->paymentId);
            }

            die('OK');
        } catch (\Exception $e) {
            Log::error($e);
            die('NOTOK');
        }
    }

    /**
     * @param array   $request
     * @param Payment $payment
     * @throws \Exception
     * @author Pavel Shulaev (http://rover-it.me)
     */
    /*public function checkNotification(Payment $payment, array $request)
    {
        $requestParams = $request;
        unset($requestParams['Token']);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tnot.txt', print_r($requestParams, 1), FILE_APPEND);
        $params = $this->getParams($payment);
        //$requestParams['Password'] = trim($params['SHOP_SECRET_WORD']);

        $token = \RoverTinkoffHelper::generateToken($requestParams, trim($params['SHOP_SECRET_WORD']));

        if($request['Token'] != $token)
            throw new \Exception(Loc::getMessage('rover-tp__token-error',
                array('#REQUEST#' => serialize($request))));

        RequestTinkoff::isSuccess($requestParams);

        $this->paymentStatus    = $request['Status'];
        $this->paymentId        = $request['PaymentId'];
    }*/

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }
}