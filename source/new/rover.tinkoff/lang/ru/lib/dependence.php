<?php
use \Rover\Tinkoff\Dependence;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.05.2017
 * Time: 16:36
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
$MESS['rover-tp__php_version_error']	    = "Версия php ниже #min_php_version#";
$MESS['rover-tp__no_curl_error']	        = "Не найдена библиотека CURL";
$MESS['rover-tp__main-version-error']      = 'Требуется модуль «Главный модуль» (main) версии ' . Dependence::MIN_VERSION__MAIN . ' или старше. Обновите его в <a href="/bitrix/admin/update_system.php">разделе обновления платформы</a>.';

$MESS['rover-tp__is_trial']       = 'Решение работает в демо-режиме.<br>Вы можете преобрести «Интернет-эквайринг Тинькофф Банк (Tinkoff Bank, приём платежей)» на <a href="http://marketplace.1c-bitrix.ru/solutions/rover.tinkoff/">Битрикс Маркетплейс</a>.';
$MESS['rover-tp__trial_expired']  = 'Демо-период истёк.<br>Вы можете преобрести «Интернет-эквайринг Тинькофф Банк (Tinkoff Bank, приём платежей)» на <a href="http://marketplace.1c-bitrix.ru/solutions/rover.tinkoff/">Битрикс Маркетплейс</a>.';
$MESS['rover-tp__writable-error'] = 'Файл "#path#" недоступен для записи';
$MESS['rover-tp__mkdir-error']    = 'Не удалось создать директрию "#dir#"';
