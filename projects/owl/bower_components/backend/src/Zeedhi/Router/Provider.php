<?php
namespace Zeedhi\Router;

use Zeedhi\DTO\Response;
use Zeedhi\HTTP\Response\JSON;

/**
 * A router that contains many instances of routes.
 *
 * @author tulio
 * 
 */
class Provider {

    protected $request;
    /** @var  Response */
    protected $response;
    protected $route;
    protected $config;
    protected $errorHandler;
    protected $application;
    protected $initialized;

    /**
     * @param \Zeedhi\Application $application
     * @param \Zeedhi\Config\RouterConfig $config
     */
    public function __construct(\Zeedhi\Application $application, \Zeedhi\Config\RouterConfig $config) {
        $this->config = $config;
        $this->application = $application;
        $this->initialized = false;
    }

    public function sendResponse() {
        $statusCode = $this->response->getStatus() == Response::STATUS_SUCCESS ?
            \Zeedhi\HTTP\Response::STATUS_CODE_OK :
            \Zeedhi\HTTP\Response::STATUS_CODE_INTERNAL_SERVER_ERROR;

        $content = array();
        if($datasets = $this->response->getDataSets()) {
            $content['dataset'] = array();
            foreach($datasets as $dataset) {
                $content['dataset'][$dataset->getId()] = $dataset->getContent();
            }
        }

        if($error = $this->response->getError()) {
            $content['error'] = $error->getMessage();
        }

        if($messages = $this->response->getMessages()) {
            $content['messages'] = array();
            foreach($messages as $key => $message) {
                $content['messages'][$key]['message'] = $message->getMessage();
                $content['messages'][$key]['type'] = $message->getType();
//                $content['messages'][$key]['fadeTime'] = $message->getFadeTime();
            }
        }

        if($methods = $this->response->getMethods()) {
            $content['method'] = array();
            foreach($methods as $key => $method) {
                $content['method'][$key]['name'] = $method->getName();
                $content['method'][$key]['parameters'] = $method->getParameters();
            }
        }
        $httpResponse = new JSON($content, $statusCode);
        $httpResponse->send();
    }

    public function run() {
        try {
            $this->init();
            $this->beforeInvoke($this->getRoute());
//            $this->preDispatch($request);
            $this->dispatchRoute();
//            $this->postDispatch($request, $response);
            $this->afterInvoke($this->request, $this->response);
//            $this->afterInvoke($request, $response);
            $this->sendResponse();
        } catch(\Exception $e) {
            $this->handleException($e);
        }
        exit;
    }

    /**
     * Initialize route if exists, otherwise exit with a HTTP 404 error.
     */
    public function initRoute() {
        try{
            $httpRequest = new \Zeedhi\Request\HttpRequest($this->getBaseUri());
            $this->route = new \Zeedhi\Router\Route($httpRequest);
        } catch(\Zeedhi\Router\Exception\RouteNotFound $e) {
//            header("HTTP/1.1 404 Not Found");
            $response = $this->handleNotFound();
            if ($response !== false) echo $response;
            exit;
        }
    }

    /**
     * Initialize request.
     */
    public function initRequest() {
        $route = $this->getRoute();
        $this->request = \Zeedhi\Request\RequestFactory::factory($route->getEventType(), $route->getHttpData());
    }

    /**
     * Dispatch route.
     */
    public function dispatchRoute() {
        $this->getRoute()->dispatch($this->application, $this->request , $this->response);
    }

    /**
     * Handle 404 Not found HTTP error.
     */
    public function handleNotFound() {
        $listeners = $this->getConfig()->getNotFoundListeners();
        foreach($listeners as $listener) {
            $listener->onNotFound();
        }

        $handler = $this->getConfig()->getNotFoundHandler();
        if ($handler) {
            return $this->getNotFoundHandler()->handleNotFound();
        }

        return false;
    }

    /**
     * @todo Call listeners by type (interfaces: LogFileException)
     */
    public function handleException(\Exception $exception) {
        header('HTTP/1.1 500 Internal Server Error');
        foreach($this->getExceptionListeners() as $listener) {
            $listener->beforeHandleException();
        }

        /** @TODO adjust exception handling */
        $handlers = $this->getExceptionHandlers();
        foreach($handlers as $exceptionClass=>$handler) {
            if($exception instanceof $exceptionClass)
                echo $handler->handleException($exception);
        }
    }

    /**
    * 
    *
    *
    **/
    public function handleError($errno, $errstr, $errfile, $errline){
        header('HTTP/1.1 500 Internal Server Error');
        if($this->errorHandler){
            $this->errorHandler->handleError($errno, $errstr, $errfile, $errline);
        }
    }

    /**
     * Event that occurs after initialization of a route and immediately before processing it.
     * 
     * @param Route $route
     */
    public function beforeInvoke($route) {
        $securityListeners = $this->getSecurityInterceptors();
        foreach ($securityListeners as $securityListener) {
            $securityListener->beforeInvoke($route);
        }
    }

    /**
     * Event that occurs after processing of a route.
     * 
     * @param type $request
     * @param type $response
     */
    public function afterInvoke($request, $response) {
        $securityListeners = $this->getSecurityInterceptors();
        foreach ($securityListeners as $securityListener) {
            $securityListener->afterInvoke($request, $response);
        }
    }

    /**
     * Event that occurs before dispatch a route.
     * 
     * @param type $request
     */
    public function preDispatch($request) {
        
    }

    /**
     * Event that occurs after dispatch a route.
     * @param type $request
     */
    public function postDispatch($request, $response) {
        
    }

    /**
     *
     * @return \Zeedhi\Request\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     *
     * @return Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * 
     * @return \Zeedhi\Config\RouterConfig
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * 
     * @return string
     */
    public function getBaseUri() {
        return $this->getConfig()->getBaseUri();
    }

    /**
     * 
     * @return array of Listener\INotFound
     */
    public function getNotFoundListeners() {
        return $this->getConfig()->getNotFoundListeners();
    }

    /**
     * 
     * @return Handler\NotFoundHandler
     */
    public function getNotFoundHandler() {
        return $this->getConfig()->getNotFoundHandler();
    }

    /**
     * 
     * @return array of Listener\SecurityListener
     */
    public function getSecurityInterceptors() {
        return $this->getConfig()->getSecurityInterceptors();
    }

    /**
     * 
     * @return Handler\ExceptionHandler[]
     */
    public function getExceptionHandlers() {
        return $this->getConfig()->getExceptionHandlers();
    }

    /**
     * 
     * @return Handler\ExceptionListener[]
     */
    public function getExceptionListeners() {
        return $this->getConfig()->getExceptionListeners();
    }

    /**
     * 
     * @return Handler\ErrorHandler
     */
    public function getErrorHandler() {
        return $this->errorHandler;
    }

    /**
     * 
     * @param Handler\ErrorHandler $errorHandler
     */
    public function setErrorHandler(Handler\ErrorHandler $errorHandler) {
        $this->errorHandler = $errorHandler;
//        set_error_handler(array("Provider", 'handleError'));
    }

    public function init()
    {
        if ($this->initialized === false) {
            $this->response = new \Zeedhi\DTO\Response();
            $this->initRoute();
            $this->initRequest();
            $this->initialized = true;
        }
    }

    /**
     * @return \Zeedhi\DTO\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

}