<?php
namespace Zeedhi\Config;
use Zeedhi\ErrorHandler\ErrorHandlerService;
use Zeedhi\ErrorHandler\HandlerEntry;
use Zeedhi\ErrorHandler\ListenerEntry;

/**
 * Description of RouterConfig
 *
 * @author tuliopinto
 */
class RouterConfig {

    /** @var  string */
    private $baseUri;
    /** @var array  */
    private $configData;
    private $notFoundHandler;
    private $notFoundListeners = array();
    private $exceptionHandlers = array();
    private $exceptionListeners = array();
    private $securityInterceptors = array();

    public function __construct(array $configData) {
        $this->configData = $configData;
        $this->baseUri = $configData['baseUri'];
    }

    /**
     * 
     * @throws \Zeedhi\Router\Exception\InvalidSecurityInterceptor
     */
    private function initSecurityInterceptors() {
        $listeners = $this->configData['securityInterceptors'];
        foreach($listeners as $className) {
            if(class_exists($className) === false) {
                throw new \Zeedhi\Router\Exception\InvalidSecurityInterceptor();
            }

            $listener = new $className();
            if($listener instanceof \Zeedhi\Router\Interceptor\SecurityInterceptor) {
                $this->addSecurityInterceptor($listener);
            } else {
                throw new \Zeedhi\Router\Exception\InvalidSecurityInterceptor();
            }
        }
    }

    /**
     * 
     * @param string $className
     * @throws \Zeedhi\Router\Exception\InvalidNotFoundHandler
     */
    public function initNotFoundHandler() {
        $className = $this->configData['notFoundHandler'];
        if(class_exists($className) === false) {
            throw new \Zeedhi\Router\Exception\InvalidNotFoundHandler();
        }

        $handler = new $className();
        if($handler instanceof \Zeedhi\Router\Handler\NotFoundHandler) {
            $this->setNotFoundHandler($handler);
        } else {
            throw new \Zeedhi\Router\Exception\InvalidNotFoundHandler();
        }
    }

    /**
     * Initialize Not Found Listeners.
     */
    public function initNotFoundListeners() {
        $listeners = $this->configData['notFoundListeners'];

        foreach ($listeners as $listener) {
            $this->initNotFoundListener($listener);
        }
    }

    /**
     * 
     * @param string $exceptionType
     * @param string $listenerName
     * @throws \Zeedhi\Router\Exception\InvalidExceptionHandler
     */
    public function initNotFoundListener($listenerName) {
        if(class_exists($listenerName) === false) {
            throw new \Zeedhi\Router\Exception\InvalidNotFoundListener();
        }

        $listener = new $listenerName();
        if($listener instanceof \Zeedhi\Router\Listener\NotFoundListener) {
            $this->addNotFoundListener($listener);
        } else {
            throw new \Zeedhi\Router\Exception\InvalidNotFoundListener();
        }
    }

    /**
     * Initialize Exception Handlers.
     */
    public function initExceptionHandlers() {
        $handlers = $this->configData['exceptionHandlers'];
        foreach ($handlers as $handler) {
            $this->initExceptionHandler($handler['exception'], $handler['handler']);
        }
    }

    /**
     * 
     * @param string $exceptionName
     * @param string $handlerName
     * @throws \Zeedhi\Router\Exception\InvalidExceptionHandler
     */
    public function initExceptionHandler($exceptionName, $handlerName) {
        if(class_exists($handlerName) === false) {
            throw new \Zeedhi\Router\Exception\InvalidExceptionHandler();
        }

        $handler = new $handlerName();
        if($handler instanceof \Zeedhi\Router\Handler\ExceptionHandler) {
            $this->addExceptionHandler($exceptionName, $handler);
        } else {
            throw new \Zeedhi\Router\Exception\InvalidExceptionHandler();
        }
    }

    /**
     * Initialize Exception Handlers.
     */
    public function initExceptionListeners() {
        $listeners = $this->configData['exceptionListeners'];

        foreach ($listeners as $listener) {
            $this->initExceptionListener($listener['interface'], $listener['listener']);
        }
    }

    /**
     * 
     * @param string $exceptionType
     * @param string $listenerName
     * @throws \Zeedhi\Router\Exception\InvalidExceptionHandler
     */
    public function initExceptionListener($exceptionType, $listenerName) {
        if(class_exists($listenerName) === false) {
            throw new \Zeedhi\Router\Exception\InvalidExceptionHandler();
        }

        $listener = new $listenerName();
        if($listener instanceof \Zeedhi\Router\Handler\ExceptionHandler) {
            $this->addExceptionHandler($exceptionType, $listener);
        } else {
            throw new \Zeedhi\Router\Exception\InvalidExceptionHandler();
        }
    }
    
    /**
     * 
     * @return \Zeedhi\Router\Listener\NotFoundListener
     */
    public function getNotFoundListeners() {
        if(empty($this->notFoundListeners)) {
            $this->initNotFoundListeners();
        }

        return $this->notFoundListeners;
    }

    /**
     * 
     * @param \Zeedhi\Router\Listener\NotFoundListener $listener
     */
    public function addNotFoundListener(\Zeedhi\Router\Listener\NotFoundListener $listener) {
        $this->notFoundListeners[] = $listener;
    }

    /**
     * 
     * @return \Zeedhi\Router\Handler\NotFoundHandler
     */
    public function getNotFoundHandler() {
        if($this->notFoundHandler === null) {
            $this->initNotFoundHandler();
        }

        return $this->notFoundHandler;
    }

    /**
     * 
     * @param \Zeedhi\Router\Handler\NotFoundHandler $handler
     */
    public function setNotFoundHandler(\Zeedhi\Router\Handler\NotFoundHandler $handler) {
        $this->notFoundHandler = $handler;
    }

    /**
     * 
     * @return \Zeedhi\Router\Interceptor\SecurityInterceptor[]
     */
    public function getSecurityInterceptors() {
        if(empty($this->securityInterceptors)) {
            $this->initSecurityInterceptors();
        }

        return $this->securityInterceptors;
    }

    /**
     * 
     * @param \Zeedhi\Router\Interceptor\SecurityInterceptor $listener
     */
    public function addSecurityInterceptor(\Zeedhi\Router\Interceptor\SecurityInterceptor $listener) {
        $this->securityInterceptors[] = $listener;
    }

    /**
     * 
     * @return \Zeedhi\Router\Handler\ExceptionHandler[]
     */
    public function getExceptionHandlers() {
        if(empty($this->exceptionHandlers)) {
            $this->initExceptionHandlers();
        }

        return $this->exceptionHandlers;
    }

    /**
     * 
     * @param string $exceptionName
     * @param \Zeedhi\Router\Handler\ExceptionHandler $handler
     */
    public function addExceptionHandler($exceptionName, \Zeedhi\Router\Handler\ExceptionHandler $handler) {
        $this->exceptionHandlers[$exceptionName] = $handler;
    }

    /**
     * 
     * @return \Zeedhi\Router\Listener\ExceptionListener[]
     */
    public function getExceptionListeners() {
        if(empty($this->exceptionListeners)) {
            $this->initExceptionListeners();
        }

        return $this->exceptionListeners;
    }

    /**
     * 
     * @param string $exceptionType
     * @param \Zeedhi\Router\Listener\ExceptionListener $listener
     */
    public function addExceptionListener($exceptionType, \Zeedhi\Router\Listener\ExceptionListener $listener) {
        $this->exceptionListeners[$exceptionType] = $listener;
    }

    /**
     * @return string
     */
    public function getBaseUri() {
        return $this->baseUri;
    }

    public function populateErrorHandler(ErrorHandlerService $errorHandler) {
        foreach($this->configData['errorHandler'] as $configEntry) {
            $errorHandler->addHandlerEntry(new HandlerEntry($configEntry['handler'], $configEntry['errorCodes']));
        }

        foreach($this->configData['errorListeners'] as $configEntry) {
            $errorHandler->addListenerEntry(new ListenerEntry($configEntry['listener'], $configEntry['errorCodes']));
        }
    }

}