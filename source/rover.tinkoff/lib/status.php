<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 05.11.2017
 * Time: 10:46
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Tinkoff;

use Bitrix\Main\Localization\Loc;
use Sale\Handlers\PaySystem\rover_tinkoffHandler;

Loc::loadMessages(__FILE__);
/**
 * Class Status
 *
 * @package Rover\Tinkoff
 * @author  Pavel Shulaev (https://rover-it.me)
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
     * @param rover_tinkoffHandler $handler
     * @return bool
     * @throws \Exception
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isOrderFailed(rover_tinkoffHandler $handler)
    {
        return self::is($handler, array(Status::STATUS_CANCELED, Status::STATUS_REVERSED, Status::STATUS_REJECTED));
    }

    /**
     * @param rover_tinkoffHandler $handler
     * @return bool
     * @throws \Exception
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isOrderPaid(rover_tinkoffHandler $handler)
    {
        return self::is($handler, Status::STATUS_CONFIRMED);
    }

    /**
     * @param rover_tinkoffHandler $handler
     * @return bool
     * @throws \Exception
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isOrderRefunded(rover_tinkoffHandler $handler)
    {
        return self::is($handler, self::STATUS_REFUNDED);
    }

    /**
     * @param rover_tinkoffHandler $handler
     * @return bool
     * @throws \Exception
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isAuthorized(rover_tinkoffHandler $handler)
    {
        return self::is($handler, self::STATUS_AUTHORIZED);
    }

    /**
     * @param rover_tinkoffHandler $handler
     * @param                      $statuses
     * @return bool
     * @throws \Exception
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function is(rover_tinkoffHandler $handler, $statuses)
    {
        self::check($handler);

        if (!is_array($statuses))
            $statuses = array($statuses);

        return in_array($handler->getPaymentStatus(), $statuses);
    }

    /**
     * Check is status variable is set
     *
     * @param rover_tinkoffHandler $handler
     * @throws \Exception
     * @author Pavel Shulaev (https://rover-it.me)
     */
    private static function check(rover_tinkoffHandler $handler)
    {
        if(is_null($handler->getPaymentStatus()))
            throw new \Exception(Loc::getMessage('rover-tp__status-error'));
    }
}