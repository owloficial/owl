<?php
namespace Zeedhi\Callback;

use Zeedhi\Application;

class Service {

    /** @var static */
    private static $instance;
    /** @var Application */
    protected $application;
    /** @var array */
    protected $methods;

    function __construct(Application $application)
    {
        $this->application = $application;
    }
    
    /**
     * @return Service|static
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new static(Application::getInstance());
        }

        return self::$instance;
    }


    public function apply($entity, $eventName) {
        foreach($this->readEntityEvents($entity, $eventName) as $method) {
            $this->run($method, $entity);
        }
    }

    protected function run($method, $entity) {
        call_user_func($this->getCallAble($method), $entity);
    }

    protected function getCallAble($method) {
        if(!isset($this->methods[$method])) {
            $this->methods[$method] = new $method($this->application);
        }

        return array($this->methods[$method], "apply");
    }

    protected function readEntityEvents($entity, $eventName) {
        $className = get_class($entity);
        $events = $className::$events;
        return $events[$eventName];
    }
}