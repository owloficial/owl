<?php
namespace Zeedhi\Request\Response\IResponse;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 13:38
 * To change this template use File | Settings | File Templates.
 */
class EventResponse extends \Zeedhi\Request\Response\IResponse\Response
{

    private $methodToCall;
    private $fieldsToUpdate;
    private $windowToOpen;

    public function toJson()
    {
        return "{methodToCall: " . $this->getMethodToCall() . "},
                {fieldsToUpdate: " .json_encode($this->getFieldsToUpdate()). "},
                {windowToOpen: " . $this->getWindowToOpen() . "}";
    }

    public function setFieldsToUpdate($fieldsToUpdate)
    {
        $this->fieldsToUpdate = $fieldsToUpdate;
    }

    public function getFieldsToUpdate()
    {
        return $this->fieldsToUpdate;
    }

    public function setMethodToCall($methodToCall)
    {
        $this->methodToCall = $methodToCall;
    }

    public function getMethodToCall()
    {
        return $this->methodToCall;
    }

    public function setWindowToOpen($windowToOpen)
    {
        $this->windowToOpen = $windowToOpen;
    }

    public function getWindowToOpen()
    {
        return $this->windowToOpen;
    }

}
