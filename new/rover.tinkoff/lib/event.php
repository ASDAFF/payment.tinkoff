<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 14.07.2017
 * Time: 17:03
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Tinkoff;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Event as MainEvent;
use Bitrix\Main\EventResult;

/**
 * Class Event
 *
 * @package Rover\AmoCRM\Helper
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Event
{
    /** @var bool */
    protected $success = true;

    /** @var string */
    protected $name;

    /** @var array */
    protected $parameters = array();

    /** @var array */
    protected $finalParameters = array();

    /**
     * Event constructor.
     *
     * @param       $name
     * @param array $parameters
     * @throws ArgumentNullException
     */
    public function __construct($name, array $parameters = array())
    {
        $name = trim($name);
        if (empty($name))
            throw new ArgumentNullException('name');

        $this->name         = $name;
        $this->parameters   = $parameters;
    }


    /**
     * @return array
     */
    public function getFinalParameters()
    {
        return $this->finalParameters;
    }

    /**
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function handle()
    {
        $event = new MainEvent('rover.tinkoff', $this->name, $this->parameters);
        $event->send();

        $results        = $event->getResults();
        $resultsCount   = count($results);
        if ($resultsCount) {
            for ($i = 0; $i < $resultsCount; $i++){
                $eventResult = $results[$i];
                switch($eventResult->getType()):
                    case EventResult::ERROR:
                        $this->success = false;
                        break(2);
                    case EventResult::SUCCESS:
                        $this->finalParameters = $eventResult->getParameters();
                        break;
                    case EventResult::UNDEFINED:
                    default:
                        break;
                endswitch;
            }
        } else {
            $this->finalParameters = $this->parameters;
        }

        return $this;
    }

    /**
     * @return bool
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return array|bool
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function run()
    {
        $args = func_get_args();
        $name = array_shift($args);

        try{
            $event = new self($name, $args);
            $event->handle();
            if ($event->isSuccess())
                return $event->getFinalParameters();
        } catch (\Exception $e) {
           Log::error($e->getMessage());
        }

        return false;
    }
}