<?
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказ оплачен");
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/payment_tinkoff/result.php");
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>