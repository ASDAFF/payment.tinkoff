<?php
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

use \Payment\Tinkoff\Dependence;

$MESS['payment-tp__php_version_error']	    = "Версия php ниже #min_php_version#";
$MESS['payment-tp__no_curl_error']	        = "Не найдена библиотека CURL";
$MESS['payment-tp__main-version-error']      = 'Требуется модуль «Главный модуль» (main) версии ' . Dependence::MIN_VERSION__MAIN . ' или старше. Обновите его в <a href="/bitrix/admin/update_system.php">разделе обновления платформы</a>.';

$MESS['payment-tp__is_trial']       = 'Решение работает в демо-режиме.';
$MESS['payment-tp__trial_expired']  = 'Демо-период истёк.';
$MESS['payment-tp__writable-error'] = 'Файл "#path#" недоступен для записи';
$MESS['payment-tp__mkdir-error']    = 'Не удалось создать директрию "#dir#"';
