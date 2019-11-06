<?php
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;
use \Payment\Tinkoff\Log;

if (!Loader::includeModule('sale')
    || !Loader::includeModule('payment.tinkoff'))
	die('NOTOK');

$request = (array)json_decode(file_get_contents("php://input"));

try{
    /** @var $order Order */
    $order      = Order::loadByAccountNumber($request['OrderId']);

    $paymentCollection = $order->getPaymentCollection();
    /** @var Payment $payment */
    foreach ($paymentCollection as $payment)
    {
        if (intval($payment->getPaymentSystemId()) > 0) {
            $paySystemService = PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
            $paySystemService->check($payment);
        }
    }
} catch (\Exception $e) {
    Log::error($e);
    die('NOTOK');
}