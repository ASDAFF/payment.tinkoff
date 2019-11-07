<?php
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Payment\Tinkoff;

use \Bitrix\Sale\Order;

/**
 * Class OrderProps
 * @package Payment\Tinkoff
 */
class OrderProps
{
    /**
     * @param Order $orderAccountNum
     * @return array
     *
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