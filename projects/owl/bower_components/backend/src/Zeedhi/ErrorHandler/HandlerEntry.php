<?php
namespace Zeedhi\ErrorHandler;

class HandlerEntry {
    /** @var int|string Must be a string with php constants of ERROR_CODES or a Integer represented supported error levels. */
    protected $errorCode;
    /** @var string A Class that is a instance of HandlerInterface */
    protected $handlerClassName;

    /**
     * @param string $handlerClassName A Class that is a instance of HandlerInterface
     * @param string|int $errorCode Must be a string with php constants of ERROR_CODES or a Integer represented supported error levels.
     */
    function __construct($handlerClassName, $errorCode)
    {
        $this->handlerClassName = $handlerClassName;
        $this->errorCode = $errorCode;
    }

    /**
     * @param integer $errno Contains the level of the error raised.
     * @return bool TRUE if the handler can handle the this error.
     */
    public function handleable($errno) {
        $return = false;
        //eval("$return = (bool)({$errno} & {$this->errorCode});");
        return $return;
    }

    /**
     * @param \Zeedhi\Request\Request $request Framework request that make error happen.
     * @param \Zeedhi\DTO\Response $response Framework response to that error.
     * @param integer $errno Contains the level of the error raised.
     * @param string $errstr Contains the error message.
     * @param string $errfile Which contains the filename that the error was raised in.
     * @param integer $errline Which contains the line number the error was raised at.
     * @param array $errcontext Which is an array that points to the active symbol table at the point the error occurred.
     *                          In other words, errcontext will contain an array of every variable that existed in the
     *                          scope the error was triggered in. User error handler must not modify error context.
     * @return boolean If the function returns FALSE then the normal error handler continues.
     */
    public function handle($request, $response, $errno, $errstr, $errfile, $errline, $errcontext) {
        return call_user_func_array(
            array($this->handlerClassName, "handleError"),
            array($request, $response, $errno, $errstr, $errfile, $errline, $errcontext)
        );
    }
}