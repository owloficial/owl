<?php
namespace Zeedhi\ErrorHandler;

interface ListenerInterface {

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
    public function listenError($errno, $errstr, $errfile, $errline, $errcontext);
}