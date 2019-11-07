<?
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

use Bitrix\Sale\Payment;
use Sale\Handlers\PaySystem\payment_tinkoffHandler;
use Bitrix\Main\Loader;
use Payment\Tinkoff\Log;
use \Bitrix\Sale\Order;
use \Payment\Tinkoff\OrderProps;
use \Bitrix\Sale\Delivery\Services\Table as DeliveryServices;

if (!Loader::includeModule('sale')) throw new \Bitrix\Main\SystemException('sale module not found');



class PaymentTinkoffHelper
{

    public static function getExtraParams(payment_tinkoffHandler $handler, $params, Payment $payment)
    {
        Log::note('OrderId=' . $params['ORDER_ID'] . '; Price=' . round($params['SHOULD_PAY'] * 100));

        try {
            $extraParams['FORM_PARAMS']     = $handler->initPayment($payment);
            $extraParams['READY_TO_PAY']    = true;

            Log::note('OrderId='.$params['ORDER_ID'] ." good payment");

        } catch (\Exception $e) {
            $extraParams['READY_TO_PAY']    = false;
            $extraParams['MESSAGE']         = $e->getMessage();

            Log::error('OrderId='.$params['ORDER_ID'] ." ". $e);
        }

        return $extraParams;
    }

    public static function getRefundParams($params, $arOrder)
    {
        $requestParams = array(
            'TerminalKey'   => $params['TERMINAL_ID'],
            'PaymentId'     => $arOrder['PAY_VOUCHER_NUM'],
            //'Password'      => $params['SHOP_SECRET_WORD'],
            //'Amount'        => round($summ * 100)
        );

        $requestParams['Token'] = self::generateToken($requestParams, trim($params['SHOP_SECRET_WORD']));

        return $requestParams;
    }

    public static function getInitParams(Payment $payment, array $params)
    {
        $requestParams = array(
            'TerminalKey'   => trim($params['TERMINAL_ID']),
            'Amount'        => round($params['SHOULD_PAY'] * 100),
            'OrderId'       => trim($params['ORDER_ID']),
            //'Password'      => trim($params['SHOP_SECRET_WORD'])
        );

        $requestParams['Token'] = self::generateToken($requestParams, trim($params['SHOP_SECRET_WORD']));

        $paySystem = $payment->getPaySystem();
        if (($paySystem->getField('CAN_PRINT_CHECK') == 'Y')
            && (!empty($params['TAXATION']))
            && (!empty($params['NDS'])))
            try {
            $requestParams['Receipt'] = self::getReceipt($params);
        } catch (\Exception $error) {
            Log::error('Init getReceipt:' . $error->getMessage());
        }
        return $requestParams;
    }

    private static function getReceipt($params)
    {
        $order = Order::loadByAccountNumber($params['ORDER_ID']);
        if (!$order) return [];

        $propsCollection = $order->getPropertyCollection();

        $receipt    = array(
            'Items'     => array(),
            //'Email'     => $propsCollection->getUserEmail()->getValue(),
            //'Phone'     => $propsCollection->getPhone()->getValue(),
            'Taxation'  => $params['TAXATION'],
        );

        if (($params['RECIPIENT_ID'] != 'email') && ($propsCollection->getUserEmail())) {
            $receipt['Email'] = $propsCollection->getUserEmail()->getValue();
        }

        if (($params['RECIPIENT_ID'] != 'phone') && ($propsCollection->getPhone())) {
            $receipt['Phone'] = $propsCollection->getPhone()->getValue();
        }

        $items = $order->getBasket()->getBasketItems();

        foreach ($items as $item) {
            $data = $item->getFieldValues();

            if ($data['PRICE'] <= 0) continue;

            $receipt['Items'][] = array(
                'Name'      => substr(mb_convert_encoding($data['NAME'], "UTF-8", LANG_CHARSET), 0, 64),
                'Price'     => round($data['PRICE'] * 100),
                'Quantity'  => round($data['QUANTITY'], 3, PHP_ROUND_HALF_UP),
                'Amount'    => round($data['PRICE'] * $data['QUANTITY'] * 100),
                'Tax'       => $params['NDS']
            );
        }

        $deliveryPrice = $order->getDeliveryPrice();
        if ($deliveryPrice) {
            $deliveryRow = self::getDeliveryRow($order);
            if (!$deliveryRow)
                throw new \Bitrix\Main\SystemException('delivery system not found');

            $receipt['Items'][] = array(
                'Name'      => substr(mb_convert_encoding($deliveryRow['NAME'], "UTF-8", LANG_CHARSET), 0, 64),
                "Price"     => round($deliveryPrice * 100),
                "Quantity"  => 1,
                "Amount"    => round($deliveryPrice * 100),
                "Tax"       => $params['NDS'],
            );
        }

        $buildReceipt = \Payment\Tinkoff\Event::run('afterBuildReceipt', $order, $receipt, $params);
        if (isset($buildReceipt[1])) return $buildReceipt[1];
        return [];
    }

    private static function getDeliveryRow(Order $order)
    {
        $deliverySystemsId  = reset($order->getDeliverySystemId());
        $result             = null;

        if (intval($deliverySystemsId)) {
            $result = DeliveryServices::getRowById($deliverySystemsId);
        } elseif (strlen($deliverySystemsId)){
            $result = DeliveryServices::getRow(array('filter' => array('=CODE' => $deliverySystemsId)));
        }

        if (isset($result['PARENT_ID'])) {
            $parentResult = DeliveryServices::getByPrimary($result['PARENT_ID'], array(
                'select' => array('NAME')
            ))->fetch();
            $result['NAME'] = $parentResult['NAME'] . ' (' . $result['NAME'] . ')';
        }

        return $result;
    }

    private static function generateToken(array $params, $password)
    {
        $tokenParams = $params;

        unset($tokenParams['DATA']);
        unset($tokenParams['Receipt']);

        $tokenParams['Password'] = $password;

        ksort($tokenParams);
        $values = implode('', array_values($tokenParams));

        return hash('sha256', $values);
    }
};

?>