<?php
namespace Zeedhi\Request;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 11:58
 * To change this template use File | Settings | File Templates.
 */
class EventRequestDataSet extends \Zeedhi\Request\EventRequest
{

    private $dataSet;

    public function setDataSet($dataSet)
    {
        $this->dataSet = $dataSet;
    }

    public function getDataSet()
    {
        return $this->dataSet;
    }

}
