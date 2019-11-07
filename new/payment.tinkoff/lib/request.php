<?php
namespace Payment\Tinkoff;

use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Web\Json;

Loc::loadMessages(__FILE__);

/**
 * Class Request
 *
 * @package Payment\Tinkoff
 */
class Request
{
    const METHOD__INIT      = 'Init';
    const METHOD__REFUND    = 'Cancel';

    const URL__V1 = 'https://securepay.tinkoff.ru/rest/';
    const URL__V2 = 'https://securepay.tinkoff.ru/v2/';

    /**
     * @param $method
     * @return string
     */
    public static function getUrl($method)
    {
        return self::getPaymentUrl() . $method;
    }

    /**
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public static function init($params)
    {
        return self::send(self::getUrl(self::METHOD__INIT), $params);
    }

    /**
     * @param $params
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function refund($params)
    {
        return self::send(self::getUrl(self::METHOD__REFUND), $params);
    }

    /**
     * @return string
     */
    protected static function getPaymentUrl()
    {
        return self::URL__V2;
    }

    /**
     * @param $url
     * @param $params
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function send($url, $params)
    {
        $myCurl = curl_init();

        if (is_array($params))
            $params = Json::encode($params);

        curl_setopt_array($myCurl, array(
            CURLOPT_URL             => $url,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => $params,
            //CURLOPT_HTTPHEADER      => array('Content-Type: application/json')
        ));

        if (self::getPaymentUrl() == self::URL__V2)
            curl_setopt($myCurl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $resultString = curl_exec($myCurl);
        curl_close($myCurl);

        //log
        Log::note('request url='. $url ."\nparams: {$params}\nresult:" . $resultString);

        $result = json_decode($resultString, true);

        // check json format
        if (json_last_error() != JSON_ERROR_NONE)
            throw new \Exception(Loc::getMessage('payment-tp__connection-error'));

        // check status
        self::isSuccess($result);

        return $result;
    }

    /**
     * @param $result
     * @throws \Exception
     */
    public static function isSuccess($result)
    {
        if ($result['Success'] != true)
            throw new \Exception(Loc::getMessage('payment-tp__request-error'));
    }
}