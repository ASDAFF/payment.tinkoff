<?php
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
/**
 * @var \CMain $APPLICATION;
 */
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\SystemException;
use \Bitrix\Sale\Order;

if (!Loader::includeModule('sale'))
	throw new SystemException('sale module not found');

Loc::loadMessages(__FILE__);

$APPLICATION->SetPageProperty("title", Loc::getMessage('payment-t__page-title'));
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle(Loc::getMessage('payment-t__title'));

$request    = Application::getInstance()->getContext()->getRequest();
$orderID    = $request->get('OrderId');
/** @var $order Order */
$order      = Order::loadByAccountNumber($orderID);

if (!is_null($order)){
	$statusPageURL  = sprintf('%s?ID=%s', GetPagePath('personal/order'), $orderID);
	$status         = $request->get('Success') == 'true';
}

?>
<style>
	.alert{
		padding: 15px;
		border: none;
		border-radius: 1px;
		font-size: 14px;
		margin-bottom: 250px;;
	}

	.alert-info {
		background-color: #d9edf7;
		border-color: #bce8f1;
		color: #31708f;
	}

	.alert-danger {
		background-color: #f2dede;
		border-color: #ebccd1;
		color: #a94442;
	}
</style>
<h2><?=Loc::getMessage('payment-t__title')?></h2><br>
<div class="alert alert-<?=$status ? 'info' : 'danger'?>"><?php
if (!$order):

	echo Loc::getMessage('payment-t__order_not_found',
		array('#order_id#' => $orderID));

else:
	if ($status)
		echo Loc::getMessage('payment-t__order_status_success',
			array('#order_id#' => $orderID));
	else
		echo Loc::getMessage('payment-t__order_status_error',
			array('#order_id#' => $orderID));

	?><br><?=Loc::getMessage('payment-t__order_status',
	array('#status_page#' => $statusPageURL));

endif; ?></div>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>