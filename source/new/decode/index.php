<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Rover\Tinkoff\Dependence;

Loc::loadMessages(__FILE__);
include_once dirname(__FILE__) . '/../lib/dependence.php';

Class rover_tinkoff extends CModule
{
    var $MODULE_ID = 'rover.tinkoff';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    /**
     * payment_tinkoff constructor.
     */
    function __construct()
    {
        global $tinkoffErrors;

        $arModuleVersion = array();
        $tinkoffErrors = array();

        require(__DIR__ . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION		= $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE	= $arModuleVersion["VERSION_DATE"];
        } else
            $tinkoffErrors[] = Loc::getMessage('rover_t__version_info_error');

        $this->MODULE_NAME          = Loc::getMessage("rover_t__MODULE_NAME");
        $this->MODULE_DESCRIPTION   = Loc::getMessage("rover_t__MODULE_DESC");
        $this->PARTNER_NAME         = GetMessage('rover_t__PARTHER_NAME');
        $this->PARTNER_URI          = GetMessage('rover_t__PARTHER_URI');
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    /**
     * @param $from
     * @param $to
     * @throws \Bitrix\Main\SystemException
     *
     */
    protected function rewrite($from, $to)
    {
        if (!file_exists($from))
            throw new \Bitrix\Main\SystemException(Loc::getMessage('rover-tp__file-not-exists', array(
                '#file#' => $from
            )));

        if (file_exists($to))
            unlink($to);

        copy($from, $to);
    }

    /**
     * @return bool
     *
     */
    function UnInstallFiles()
    {
        DeleteDirFilesEx('/bitrix/php_interface/include/sale_payment/rover_tinkoff/');
        return true;
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     */
    protected function getSites()
    {
        $query = array(
            'select' => array('LID', 'NAME', 'DIR', 'DOC_ROOT', 'SERVER_NAME', 'SITE_NAME')
        );

        return \Bitrix\Main\SiteTable::getList($query)->fetchAll();
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     */
    function DoInstall()
    {
        global $APPLICATION, $step, $tinkoffErrors, $tinkoffSites;

        $step           = intval($step);
        $tinkoffSites   = $this->getSites();

        if ((count($tinkoffSites) > 1) && ($step < 2)){
            $APPLICATION->IncludeAdminFile(Loc::getMessage("payment-tp__install_title"),
                dirname(__FILE__) . "/step.php");
        } else {

            if (class_exists('\Payment\Tinkoff\Dependence')) {
                $dependence = new Dependence();
                $depErrors  = $dependence->checkBase()->getErrors();

                $tinkoffErrors = array_merge($tinkoffErrors, $depErrors);
            } else
                $tinkoffErrors[] = Loc::getMessage('payment-tp__dependence_error');

            if (empty($tinkoffErrors)) {

                $this->InstallDB();
                $this->InstallEvents();

                if (count($tinkoffSites) > 1) {
                    $sites  = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('tsite');
                } else {
                    $site   = reset($tinkoffSites);
                    $sites  = array($site['LID'] => 'Y');
                }

                if (empty($sites)) {
                    $tinkoffErrors[] = Loc::getMessage('payment-tp__site-id-not-found');
                } else {
                    foreach ($sites as $siteId => $siteValue)
                    {
                        if ($siteValue != 'Y') continue;

                        $this->InstallFiles(array('siteId' => $siteId));
                    }
                }
            }

            global $APPLICATION, $tinkoffErrors;

            if (empty($tinkoffErrors))
                ModuleManager::registerModule($this->MODULE_ID);

            $APPLICATION->IncludeAdminFile(Loc::getMessage("payment-tp__install_title"), dirname(__FILE__) . '/message.php');
        }
    }

    /**
     * @param array $arParams
     * @return bool|void
     *
     */
    function InstallFiles($arParams = array())
    {
        global $tinkoffErrors, $tinkoffSites;

        $siteId = isset($arParams['siteId']) ? trim($arParams['siteId']) : '';
        if (!strlen($siteId)) {
            $tinkoffErrors[] = Loc::getMessage('payment-tp__site-id-not-found');
            return;
        }

        $docRoot    = '';
        $siteRoot   = '';
        foreach ($tinkoffSites as $tSite) {
            if ($tSite['LID'] == $siteId) {

                $docRoot = empty($tSite['DOC_ROOT'])
                    ? $_SERVER['DOCUMENT_ROOT']
                    : $tSite['DOC_ROOT'];

                $siteRoot = str_replace('//', '/', $docRoot . '/' . $tSite['DIR']);
                break;
            }
        }

        if (!strlen($docRoot)){
            $tinkoffErrors[] = Loc::getMessage('payment-tp__doc-root-not-found', array(
                '#site-name#' => $siteId
            ));
            return;
        }

        if (!is_dir($ipn_dir = $docRoot . '/bitrix/php_interface/include/sale_payment/'))
            mkdir($ipn_dir, 0755);

        if (is_dir($source = $docRoot . '/bitrix/modules/' . $this->MODULE_ID . '/install/')) {

            if (!is_dir($siteRoot))
                mkdir($siteRoot, 0755);

            if (!is_dir($dir2 = $siteRoot . '/personal/'))
                mkdir($dir2, 0755);

            if (!is_dir($dir3 = $siteRoot . '/personal/order/'))
                mkdir($dir3, 0755);

            if (is_dir($dir3) && is_dir($ipn_dir)) {
                CopyDirFiles($source . "/sale_payment/", $ipn_dir, true, true);

                try{
                    $this->rewrite($source . "/notifications/notification.php", $siteRoot . '/personal/order/notification.php');
                    copy($source . "/notifications/success.php", $siteRoot . '/personal/order/success.php');
                    copy($source . "/notifications/failed.php", $siteRoot . '/personal/order/failed.php');
                } catch (\Exception $e) {
                    $tinkoffErrors[] = $e->getMessage();
                }

            } elseif (!is_dir($ipn_dir)) {
                $tinkoffErrors[] = Loc::getMessage('payment-tp__dir-error', array(
                    '#dir#' => $ipn_dir
                ));
            } else {
                $tinkoffErrors[] = Loc::getMessage('payment-tp__dir-error', array(
                    '#dir#' => $dir3
                ));
            }
        }
    }

    function DoUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $this->UnInstallDB();

        global $APPLICATION;
        $APPLICATION->IncludeAdminFile(Loc::getMessage("payment-tp__uninstall_title"),
            dirname(__FILE__) . '/unMessage.php');
    }

    function InstallDB(){}
    function UnInstallDB(){}
} ?>