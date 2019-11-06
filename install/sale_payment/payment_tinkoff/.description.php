<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$statuses = CSaleStatus::GetList(
    array('SORT' => 'ASC'),
    array('LID' => 'ru'),
    false,
    false,
    array('ID', 'NAME')
);
$resultStatuses = [];
while ($status = $statuses->Fetch())
    $resultStatuses[$status['ID']] = '[' . $status['ID'] . '] ' . $status['NAME'];

$data = array(
    'NAME'          => Loc::getMessage('SALE_TINKOFF_TITLE'),
    'DESCRIPTION'   => Loc::getMessage('SALE_TINKOFF_DESCRIPTION'),
    'CODES' => array(
        "TERMINAL_ID" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_TERMINAL_ID_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_TERMINAL_ID_DESCR"),
            "SORT" => 100,
            'GROUP' => 'GENERAL_SETTINGS'
        ),
        "ORDER_ID" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_ORDER_ID_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_ORDER_ID_DESCR"),
            'DEFAULT' => array(
                'PROVIDER_KEY' => 'ORDER',
                'PROVIDER_VALUE' => 'ACCOUNT_NUMBER'
            ),
            "SORT" => 200,
            'GROUP' => 'GENERAL_SETTINGS'
        ),
        "SHOP_SECRET_WORD" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_SHOP_SECRET_WORD_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_SHOP_SECRET_WORD_DESCR"),
            "SORT" => 300,
            'GROUP' => 'GENERAL_SETTINGS'
        ),
        /*"TINKOFF_PAYMENT_URL" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_PAYMENT_URL_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_PAYMENT_URL_DESCR"),
            'DEFAULT' => array(
                'PROVIDER_KEY' => 'VALUE',
                'PROVIDER_VALUE' => 'https://securepay.tinkoff.ru/rest/'
            ),
            "SORT" => 400,
        ),*/
        "SHOULD_PAY" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_SHOULD_PAY_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_SHOULD_PAY_DESCR"),
            'DEFAULT' => array(
                'PROVIDER_KEY' => 'PAYMENT',
                'PROVIDER_VALUE' => 'SUM'
            ),
            "SORT" => 500,
            'GROUP' => 'GENERAL_SETTINGS'
        ),
        "PAYMENT_DESCRIPTION" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_DESCRIPTION_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_DESCRIPTION_DESCR"),
            'DEFAULT' => array(
                'PROVIDER_KEY' => 'VALUE',
                'PROVIDER_VALUE' => Loc::getMessage("SALE_TINKOFF_DESCRIPTION_VALUE")
            ),
            "SORT" => 600,
            'GROUP' => 'GENERAL_SETTINGS'
        ),
        /*"RECEIPT" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_RECEIPT_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_RECEIPT_DESCR"),
            "INPUT" => array(
                'TYPE' => 'Y/N'
            ),
            'DEFAULT' => array(
                "PROVIDER_VALUE" => "N",
                "PROVIDER_KEY" => "INPUT"
            ),
            "SORT" => 700,
        ),*/
        "TAXATION" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_TAXATION_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_TAXATION_DESCR"),
            "INPUT" => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'osn'       => Loc::getMessage("SALE_TINKOFF_TAXATION_osn"),
                    'usn_income'=> Loc::getMessage("SALE_TINKOFF_TAXATION_usn_income"),
                    'usn_income_outcome' => Loc::getMessage("SALE_TINKOFF_TAXATION_usn_income_outcome"),
                    'envd'      => Loc::getMessage("SALE_TINKOFF_TAXATION_envd"),
                    'esn'       => Loc::getMessage("SALE_TINKOFF_TAXATION_esn"),
                    'patent'    => Loc::getMessage("SALE_TINKOFF_TAXATION_patent")
                )
            ),
            "SORT" => 800,
            'GROUP' => 'GENERAL_SETTINGS'
        ),
        "NDS" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_NDS_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_NDS_DESCR"),
            "INPUT" => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'none'  => Loc::getMessage("SALE_TINKOFF_NDS_none"),
                    'vat0'  => Loc::getMessage("SALE_TINKOFF_NDS_vat0"),
                    'vat10' => Loc::getMessage("SALE_TINKOFF_NDS_vat10"),
                    'vat18' => Loc::getMessage("SALE_TINKOFF_NDS_vat18"),
                    'vat110'=> Loc::getMessage("SALE_TINKOFF_NDS_vat110"),
                    'vat118'=> Loc::getMessage("SALE_TINKOFF_NDS_vat118")
                )
            ),
            "SORT" => 900,
            'GROUP' => 'GENERAL_SETTINGS'
        ),
        'RECIPIENT_ID' => [
            "NAME" => Loc::getMessage("SALE_TINKOFF_RECIPIENT_ID_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_RECIPIENT_ID_DESCR"),
            "INPUT" => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'both'  => Loc::getMessage("SALE_TINKOFF_RECIPIENT_ID_both"),
                    'email' => Loc::getMessage("SALE_TINKOFF_RECIPIENT_ID_email"),
                    'phone' => Loc::getMessage("SALE_TINKOFF_RECIPIENT_ID_phone"),
                ),
                'DEFAULT'   => 'both',
            ),

            "SORT" => 910,
            'GROUP' => 'GENERAL_SETTINGS'
        ],
        /*"LOG_ENABLED" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_LOG_ENABLED_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_LOG_ENABLED_DESCR"),
            'DEFAULT' => array(
                'PROVIDER_KEY' => 'VALUE',
                'PROVIDER_VALUE' => 0
            ),
            "SORT" => 800,
        ),*/


        "ORDER_STATUS_PAYED" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_ORDER_STATUS_PAYED_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_ORDER_STATUS_PAYED_DESCR"),
            "INPUT" => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => $resultStatuses,
                "DEFAULT" => "P"
            ),
            "SORT" => 950,
            'GROUP' => 'PAYMENT'
        ),
        "ORDER_STATUS_REFUNDED" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_ORDER_STATUS_REFUNDED_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_ORDER_STATUS_REFUNDED_DESCR"),
            "INPUT" => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => $resultStatuses,
                "DEFAULT" => "N"
            ),
            "SORT" => 1000,
            'GROUP' => 'PAYMENT'
        ),
        "CANCEL_REFUNDED_ORDER" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_CANCEL_REFUNDED_ORDER_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_CANCEL_REFUNDED_ORDER_NAME"),
            "INPUT" => array(
                'TYPE' => 'Y/N',
                "DEFAULT" => "N"
            ),
            "SORT" => 1050,
            'GROUP' => 'PAYMENT'
        ),
        "ORDER_STATUS_AUTHORIZED" => array(
            "NAME" => Loc::getMessage("SALE_TINKOFF_ORDER_STATUS_AUTHORIZED_NAME"),
            "DESCRIPTION" => Loc::getMessage("SALE_TINKOFF_ORDER_STATUS_AUTHORIZED_DESCR"),
            "INPUT" => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => $resultStatuses
            ),
            "SORT" => 1100,
            'GROUP' => 'PAYMENT'
        ),
    )
);