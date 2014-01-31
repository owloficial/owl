<?php
namespace Zeedhi\ErrorHandler;

use Zeedhi\Application;

class ErrorHandlerService {

    /** @var HandlerEntry[] */
    protected $errorHandlers;
    /** @var ListenerEntry[] */
    protected $errorListeners;
    /** @var \Zeedhi\DTO\Response */
    protected $response;
    /** @var \Zeedhi\Request\Request */
    protected $request;
    /** @var \Zeedhi\Application */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app) {
        $this->errorHandlers = array();
        $this->errorListeners = array();
        $this->app = $app;
    }

    public function addHandlerEntry(HandlerEntry $entry) {
        $this->errorHandlers[] = $entry;
    }

    public function addListenerEntry(ListenerEntry $entry) {
        $this->errorListeners[] = $entry;
    }

    /**
     * @param \Zeedhi\Request\Request $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param \Zeedhi\DTO\Response $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @param integer $errno Contains the level of the error raised.
     * @param string $errstr Contains the error message.
     * @param string $errfile Which contains the filename that the error was raised in.
     * @param integer $errline Which contains the line number the error was raised at.
     * @param array $errcontext Which is an array that points to the active symbol table at the point the error occurred.
     *                          In other words, errcontext will contain an array of every variable that existed in the
     *                          scope the error was triggered in. User error handler must not modify error context.
     * @return bool
     */
    public function handleError($errno, $errstr, $errfile, $errline, $errcontext) {
        $this->app->getRouter()->getConfig()->populateErrorHandler($this);
        foreach($this->errorListeners as $listener) {
            //Verify if listen this err level.
            if($listener->handleable($errno)) {
                $listener->listen($errno, $errstr, $errfile, $errline, $errcontext);
            }
        }

        foreach($this->errorHandlers as $handler) {
            //Handler doesn't handle this err level.
            if(!$handler->handleable($errno)) continue;
            if($handler->handle($this->request, $this->response, $errno, $errstr, $errfile, $errline, $errcontext)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This handle fatal errors that stop the script execution.
     */
    public function handleShutdown() {
        if ($error = error_get_last()) {
            switch($error['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_CORE_WARNING:
                case E_USER_ERROR:
                    $this->handleError($error['type'], $error['message'], $error['file'], $error['line'], null);
                    //5XX //@todo use a proper way to exit response.
                    var_dump($this->response);
                    var_dump(array($error['type'], $error['message'], $error['file'], $error['line']));
                    break;
            }
        }
    }

    /**
     * Register error handler and shutdown callbacks.
     */
    public function register() {
        set_error_handler(array($this, 'handleError'), E_ALL | E_STRICT);
        register_shutdown_function(array($this, 'handleShutdown'));
    }
}