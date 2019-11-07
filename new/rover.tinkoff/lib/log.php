<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 05.11.2017
 * Time: 10:45
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Tinkoff;

/**
 * Class Log
 *
 * @package Rover\Tinkoff
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Log
{
    /** @var bool*/
    protected static $enabled = false;

    /**
     * @return bool
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isEnabled()
    {
        return self::$enabled;
    }

    /**
     * @param                      $message
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function error($message)
    {
        self::write($message, 'error');
    }

    /**
     * @param                      $message
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function note($message)
    {
        self::write($message, 'note');
    }

    /**
     * @param                      $message
     * @param string               $type
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function write($message, $type = '')
    {
        if (!self::isEnabled())
            return;

        $message = trim($message);

        if (!strlen($message))
            return;

        $text = '[' . date('d.m.Y H:i:s') . '] ';

        $type = trim($type);
        if (strlen($type))
            $text .= $type . ': ';

        $text .= $message . "\n";

        file_put_contents(self::getFileName(), $text, FILE_APPEND);
    }

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function getFileName()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/rover_tinkoff/tinkoff.log';
    }
}