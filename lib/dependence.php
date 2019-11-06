<?php
/**
 * Copyright (c) 7/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Payment\Tinkoff;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


/**
 * Class Dependence
 * @package Payment\Tinkoff
 */
class Dependence
{
	const MIN_VERSION__MAIN     = '15.5.4';
    const MIN_VERSION__PHP      = 50306;

	/**
	 * @var array
	 */
	protected $errors = array();

    /**
     * @var bool
     */
	protected $result;

	/**
	 *
	 */
	public function __construct()
	{
		$this->reset();
	}

	/**
	 * @return mixed
	 *
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * @param $error
	 *
	 */
	protected function addError($error)
	{
        $this->errors[] = trim($error);
        $this->result   = false;
	}

    /**
     * @return $this
     *
     */
	public function checkTrialElapsedDays()
    {
        $this->reset();

        if (self::isDemoMode())
            $this->addError(Loc::getMessage('payment-tp__is_trial'));

        return $this;
    }

    /**
     * @return bool
     *
     */
    public static function isDemoMode()
    {
        return Loader::includeSharewareModule('payment.tinkoff') == Loader::MODULE_DEMO;
    }

    /**
     * @return bool
     *
     */
    public static function isTrialExpired()
    {
        return Loader::includeSharewareModule('payment.tinkoff') == Loader::MODULE_DEMO_EXPIRED;
    }



    /**
     * @return $this
     *
     */
	public function checkTrialExpired()
    {
        if (self::isTrialExpired())
            $this->addError(Loc::getMessage('payment-tp__trial_expired'));

        return $this;
    }

	/**
	 * @return $this
	 *
	 */
	public function checkPhpVer()
	{
        if (PHP_VERSION_ID < self::MIN_VERSION__PHP)
            $this->addError(Loc::getMessage('payment-tp__php_version_error', array(
                '#min_php_version#' => self::MIN_VERSION__PHP
            )));

		return $this;
	}

	/**
	 * @return $this
	 *
	 */
	public function checkCurl()
	{
		if (!function_exists('curl_init'))
			$this->addError(Loc::getMessage('payment-tp__no_curl_error'));

		return $this;
	}

	/**
	 * @return $this
	 *
	 */
	public function checkMainVer()
	{
		if (!CheckVersion(self::getVersion('main'), self::MIN_VERSION__MAIN))
			$this->addError(Loc::getMessage('payment-tp__main-version-error'));

		return $this;
	}

    /**
     * @param $dir
     * @return $this
     *
     */
    public function checkExists($dir)
    {
        if (!file_exists($dir) && !mkdir($dir))
            $this->addError(Loc::getMessage('payment-tp__mkdir-error', array('#dir#' => $dir)));

        return $this;
    }

	/**
	 * @return $this
	 *
	 */
	public function checkBase()
	{
		$this->reset();

		return $this
			->checkPhpVer()
			->checkMainVer()
			->checkCurl();
	}

    /**
     * @return $this
     *
     */
	public function checkCritical()
    {
        $this->reset();

        return $this
            ->checkPhpVer()
            ->checkMainVer();
    }

	/**
	 * @param $moduleName
	 * @return bool|string
	 *
	 */
	public static function getVersion($moduleName)
	{
		$moduleName = preg_replace("/[^a-zA-Z0-9_.]+/i", "", trim($moduleName));
		if ($moduleName == '')
			return false;

		if (!ModuleManager::isModuleInstalled($moduleName))
			return false;

		if ($moduleName == 'main')
		{
			if (!defined("SM_VERSION"))
				include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/version.php");

			return SM_VERSION;
		}

		$modulePath = getLocalPath("modules/".$moduleName."/install/version.php");
		if ($modulePath === false)
			return false;

		$arModuleVersion = array();
		include($_SERVER["DOCUMENT_ROOT"] . $modulePath);

		return array_key_exists("VERSION", $arModuleVersion)
			? $arModuleVersion["VERSION"]
			: false;
	}

    /**
     * @return $this
     *
     */
	public function reset()
	{
		$this->errors = array();
		$this->result = true;

		return $this;
	}

	/**
	 * @return array
	 *
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}