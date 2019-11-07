<?php
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$MESS['SALE_PS_MANAGER_GROUP_CONNECT_SETTINGS_TINKOFF'] = 'Настройки подключения Tinkoff';
$MESS['CONNECT_SETTINGS_TINKOFF'] = 'Настройки подключения Tinkoff';

$MESS["SALE_TINKOFF_TITLE"]         ="Тинькофф Банк";
$MESS['SALE_TINKOFF_DESCRIPTION']   = 'https://asdaff.github.io/';

$MESS["SALE_TINKOFF_TERMINAL_ID_NAME"]="Идентификатор терминала";
$MESS["SALE_TINKOFF_TERMINAL_ID_DESCR"]="Идентификатор терминала доступен в Личном кабинете Банка Тинькофф.";

$MESS["SALE_TINKOFF_SHOP_SECRET_WORD_NAME"]="Пароль";
$MESS["SALE_TINKOFF_SHOP_SECRET_WORD_DESCR"]="Пароль терминала доступен в Личном кабинете Банка Тинькофф.";

$MESS["SALE_TINKOFF_PAYMENT_URL_NAME"]="URL платёжного шлюза";
$MESS["SALE_TINKOFF_PAYMENT_URL_DESCR"]="Адрес платежного шлюза интернет-эквайринга Тинькофф Банка.";
$MESS["SALE_TINKOFF_PAYMENT_URL_VALUE"]="https://securepay.tinkoff.ru/rest/";

$MESS["SALE_TINKOFF_ORDER_ID_NAME"]     = "Номер заказа";
$MESS["SALE_TINKOFF_ORDER_ID_DESCR"]    = "Уникальный идентификатор заказа в системе интернет-магазина. Выберите значение \"Заказ\" ? \"Номер заказа\".";

$MESS["SALE_TINKOFF_SHOULD_PAY_NAME"]   = "Сумма к оплате";
$MESS["SALE_TINKOFF_SHOULD_PAY_DESCR"]  = "Выберите значение \"Оплата\" ? \"Цена\".";

$MESS['SALE_TINKOFF_DESCRIPTION_NAME']  = 'Описание платежа';
$MESS['SALE_TINKOFF_DESCRIPTION_DESCR'] = '';

/*$MESS['SALE_TINKOFF_LOG_ENABLED_NAME'] = 'Логирование запросов';
$MESS['SALE_TINKOFF_LOG_ENABLED_DESCR'] = '1 - включено, 0 - выключено';*/

//$MESS['SALE_TINKOFF_RECEIPT_NAME']  = 'Печатать чек';

$MESS['SALE_TINKOFF_TAXATION_NAME']  = 'Система налогообложения';
$MESS['SALE_TINKOFF_TAXATION_DESCR'] = "Обязательно укажите, если используете печать чеков через Tinkoff Bank";
$MESS['SALE_TINKOFF_TAXATION_osn'] = "общая СН";
$MESS['SALE_TINKOFF_TAXATION_usn_income'] = "упрощенная СН (доходы)";
$MESS['SALE_TINKOFF_TAXATION_usn_income_outcome'] = "упрощенная СН (доходы минус расходы)";
$MESS['SALE_TINKOFF_TAXATION_envd'] = "единый налог на вмененный доход";
$MESS['SALE_TINKOFF_TAXATION_esn'] = "единый сельскохозяйственный налог";
$MESS['SALE_TINKOFF_TAXATION_patent'] = "патентная СН";

$MESS['SALE_TINKOFF_ORDER_STATUS_PAYED_NAME']  = 'Статус оплаченного заказа';
$MESS['SALE_TINKOFF_ORDER_STATUS_PAYED_DESCR'] = "Укажите статус, в который будет переведен заказ после прохождения оплаты. По умолчанию — \"[P] Оплачен, формируется к отправке\"";

$MESS['SALE_TINKOFF_ORDER_STATUS_REFUNDED_NAME']    = 'Статус заказа, по которому сделали возврат или отмену резервирования';
$MESS['SALE_TINKOFF_ORDER_STATUS_REFUNDED_DESCR']   = "Укажите статус, в который будет переведен заказ, по которому сделали возврат. Если ничего не указано, то статус будет \"Новый заказ\"";

$MESS['SALE_TINKOFF_CANCEL_REFUNDED_ORDER_NAME']    = 'Отменять заказ, по которому сделали возврат или отмену резервирования';
$MESS['SALE_TINKOFF_CANCEL_REFUNDED_ORDER_DESCR']   = "Заказ будет отменён";

$MESS['SALE_TINKOFF_ORDER_STATUS_AUTHORIZED_NAME']  = 'Статус авторизованного заказа';
$MESS['SALE_TINKOFF_ORDER_STATUS_AUTHORIZED_DESCR'] = "Используется при двустадйином платеже. Укажите статус, в который будет переведен заказа, оплата по которому авторизована, но ещё не подтверждена. По умолчанию — \"[N] Принят, ожидается оплата\"";

$MESS['SALE_TINKOFF_ORDER_AUTHORIZED_FLAG_PAYED_NAME']  = 'Ставить флаг оплаты авторизованным заказам';
$MESS['SALE_TINKOFF_ORDER_AUTHORIZED_FLAG_PAYED_DESCR'] = "Используется при двустадийном платеже. При отмеченной галочке флаг \"Оплачен\" ставится сразу после авторизации оплаты, иначе — после подтверждения авторизации в личном кабинете Тинькофф Банка";

$MESS['SALE_TINKOFF_RECIPIENT_ID_NAME']     = 'Куда высылать покупателю чек';
$MESS['SALE_TINKOFF_RECIPIENT_ID_DESCR']    = "Канал связи, по которому покупатель получит информацию о чеке. По умолчанию чек высылается и на телефон и на email";
$MESS['SALE_TINKOFF_RECIPIENT_ID_both']     = 'на телефон и email';
$MESS['SALE_TINKOFF_RECIPIENT_ID_email']    = 'только на email';
$MESS['SALE_TINKOFF_RECIPIENT_ID_phone']    = 'только на телефон';

$MESS['SALE_TINKOFF_NDS_NAME']      = 'НДС';
$MESS['SALE_TINKOFF_NDS_DESCR']     = "Обязательно укажите, если используете печать чеков через Tinkoff Bank";
$MESS['SALE_TINKOFF_NDS_none']      = "без НДС";
$MESS['SALE_TINKOFF_NDS_vat0']      = "НДС по ставке 0%";
$MESS['SALE_TINKOFF_NDS_vat10']     = "НДС чека по ставке 10%";
$MESS['SALE_TINKOFF_NDS_vat20']     = "НДС чека по ставке 20%";
$MESS['SALE_TINKOFF_NDS_vat110']    = "НДС чека по расчетной ставке 10/110";
$MESS['SALE_TINKOFF_NDS_vat120']    = "НДС чека по расчетной ставке 20/120";