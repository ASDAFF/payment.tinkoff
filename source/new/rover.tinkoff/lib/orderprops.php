<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 05.11.2017
 * Time: 14:34
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Tinkoff;

use \Bitrix\Sale\Order;

/**
 * Class OrderProps
 *
 * @package Rover\Tinkoff
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class OrderProps
{
    /**
     * @param Order $orderAccountNum
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function get($orderAccountNum)
    {
        $result = array();
        $order = Order::loadByAccountNumber($orderAccountNum);

        $propertyCollection = $order->getPropertyCollection();
        $propsResult   = array(
            'PHONE'     => array(),
            'EMAIL'     => array()
        );

        foreach ($propertyCollection as $orderProperty){
            $props = $orderProperty->getProperty();

            if ($props['IS_PHONE'] == 'Y')
                $propsResult['PHONE'][] = $orderProperty->getValue();

            if ($props['IS_EMAIL'] == 'Y')
                $propsResult['EMAIL'][] = $orderProperty->getValue();
        }

        $result['PHONE']      = implode(', ', $propsResult['PHONE']);
        $result['EMAIL']      = implode(', ', $propsResult['EMAIL']);

        return $result;
    }
}