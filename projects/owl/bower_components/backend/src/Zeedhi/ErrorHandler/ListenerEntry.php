<?php
namespace Zeedhi\ErrorHandler;

class ListenerEntry {
    /** @var int|string Must be a string with php constants of ERROR_CODES or a Integer represented supported error levels. */
    protected $errorCode;
    /** @var string A Class that is a instance of ListenerInterface */
    protected $listenerClassName;

    /**
     * @param string $listenerClassName A Class that is a instance of ErrorListenerInterface
     * @param string|int $errorCode Must be a string with php constants of ERROR_CODES or a Integer represented supported error levels.
     */
    function __construct($listenerClassName, $errorCode)
    {
        $this->listenerClassName = $listenerClassName;
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
     * @param integer $errno Contains the level of the error raised.
     * @param string $errstr Contains the error message.
     * @param string $errfile Which contains the filename that the error was raised in.
     * @param integer $errline Which contains the line number the error was raised at.
     * @param array $errcontext Which is an array that points to the active symbol table at the point the error occurred.
     *                          In other words, errcontext will contain an array of every variable that existed in the
     *                          scope the error was triggered in. User error handler must not modify error context.
     * @return void
     */
    public function listen($errno, $errstr, $errfile, $errline, $errcontext) {
        call_user_func_array(
            array($this->listenerClassName, "listenError"),
            array($errno, $errstr, $errfile, $errline, $errcontext)
        );
    }
}