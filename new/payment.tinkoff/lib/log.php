<?php

namespace Payment\Tinkoff;

/**
 * Class Log
 *
 * @package Payment\Tinkoff
 */
class Log
{
    /** @var bool */
    protected static $enabled = false;

    /**
     * @return bool
     */
    public static function isEnabled()
    {
        return self::$enabled;
    }

    /**
     * @param                      $message
     */
    public static function error($message)
    {
        self::write($message, 'error');
    }

    /**
     * @param                      $message
     */
    public static function note($message)
    {
        self::write($message, 'note');
    }

    /**
     * @param                      $message
     * @param string $type
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
     */
    protected static function getFileName()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/payment_tinkoff/tinkoff.log';
    }
}