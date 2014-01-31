<?php
namespace Zeedhi\Request\Response;

/**
 * Description of Response
 *
 * @author tuliopinto
 */
class SimpleMessage implements IResponse{

    private $message;

    public function toJson() {
        return '{message: "'.$this->message.'"}';
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

}