<?php
namespace Zeedhi\Request;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 11:59
 * To change this template use File | Settings | File Templates.
 */
class EventRequestRow extends \Zeedhi\Request\EventRequest
{

    private $row;


    public function setRow($row)
    {
        $this->row = $row;
    }

    public function getRow()
    {
        return $this->row;
    }
}
