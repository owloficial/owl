<?php
namespace Zeedhi\DTO\Response;

class Error {

    private $message;
    private $errorCode;

    function __construct($message, $errorCode)
    {
        $this->message = $message;
        $this->errorCode = $errorCode;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}