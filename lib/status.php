<?php
namespace Payment\Tinkoff;

use Bitrix\Main\Localization\Loc;
use Sale\Handlers\PaySystem\payment_tinkoffHandler;

Loc::loadMessages(__FILE__);
/**
 * Class Status
 *
 * @package Payment\Tinkoff
 */
class Status
{
    /**
     * After calling initPayment()
     */
    const STATUS_NEW = 'NEW';

    /**
     * After calling cancelPayment()
     */
    const STATUS_CANCELED = 'CANCELED';

    /**
     * Intermediate status (transaction is in process)
     */
    const STATUS_PREAUTHORIZING = 'PREAUTHORIZING';

    /**
     * After showing payment form to the customer
     */
    const STATUS_FORMSHOWED = 'FORMSHOWED';

    /**
     * Intermediate status (transaction is in process)
     */
    const STATUS_AUTHORIZING = 'AUTHORIZING';

    /**
     * Intermediate status (transaction is in process)
     * Customer went to 3DS
     */
    const STATUS_THREEDSCHECKING = 'THREEDSCHECKING';

    /**
     * Payment rejected on 3DS
     */
    const STATUS_REJECTED = 'REJECTED';

    /**
     * Payment compete, money holded
     */
    const STATUS_AUTHORIZED = 'AUTHORIZED';

    /**
     * After calling reversePayment
     * Charge money back to customer
     * Not Implemented here
     */
    const STATUS_REVERSING = 'REVERSING';

    /**
     * Money charged back, transaction cmplete
     */
    const STATUS_REVERSED = 'REVERSED';

    /**
     * After calling confirmePayment()
     * Confirm money wright-off
     * Not Implemented here
     */
    const STATUS_CONFIRMING = 'CONFIRMING';

    /**
     * Money written off
     */
    const STATUS_CONFIRMED = 'CONFIRMED';

    /**
     * After calling refundPayment()
     * Retrive money back to customer
     * Not Implemented here
     */
    const STATUS_REFUNDING = 'REFUNDING';

    /**
     * Money is back on the customer account
     */
    const STATUS_REFUNDED = 'REFUNDED';

    /**
     * unknown status
     */
    const STATUS_UNKNOWN = 'UNKNOWN';

    /**
     * @param payment_tinkoffHandler $handler
     * @return bool
     * @throws \Exception
     */
    public static function isOrderFailed(payment_tinkoffHandler $handler)
    {
        return self::is($handler, array(Status::STATUS_CANCELED, Status::STATUS_REJECTED));
    }

    /**
     * @param payment_tinkoffHandler $handler
     * @return bool
     * @throws \Exception
     */
    public static function isOrderPaid(payment_tinkoffHandler $handler)
    {
        return self::is($handler, Status::STATUS_CONFIRMED);
    }

    /**
     * @param payment_tinkoffHandler $handler
     * @return bool
     * @throws \Exception
     */
    public static function isOrderRefunded(payment_tinkoffHandler $handler)
    {
        return self::is($handler, [self::STATUS_REFUNDED, Status::STATUS_REVERSED]);
    }

    /**
     * @param payment_tinkoffHandler $handler
     * @return bool
     * @throws \Exception
     */
    public static function isAuthorized(payment_tinkoffHandler $handler)
    {
        return self::is($handler, self::STATUS_AUTHORIZED);
    }

    /**
     * @param payment_tinkoffHandler $handler
     * @param                      $statuses
     * @return bool
     * @throws \Exception
     */
    public static function is(payment_tinkoffHandler $handler, $statuses)
    {
        self::check($handler);

        if (!is_array($statuses))
            $statuses = array($statuses);

        return in_array($handler->getPaymentStatus(), $statuses);
    }

    /**
     * Check is status variable is set
     *
     * @param payment_tinkoffHandler $handler
     * @throws \Exception
     */
    private static function check(payment_tinkoffHandler $handler)
    {
        if(is_null($handler->getPaymentStatus()))
            throw new \Exception(Loc::getMessage('payment-tp__status-error'));
    }
}