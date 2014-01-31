<?php
namespace Zeedhi\Request;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 11:59
 * To change this template use File | Settings | File Templates.
 */
class EventCell extends \Zeedhi\Request\EventRequest
{

    private $value;


    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
