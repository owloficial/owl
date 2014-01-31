<?php
namespace Zeedhi\Request\Response\IResponse;


/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 13:36
 * To change this template use File | Settings | File Templates.
 */
class FilterDataResponse extends \Zeedhi\Request\Response\IResponse\Response
{

    private $dataSet;

    public function toJson()
    {
        return '{dataset: "'.$this->getDataSet().'"}';
    }

    public function setDataSet($dataSet)
    {
        $this->dataSet = $dataSet;
    }

    public function getDataSet()
    {
        return $this->dataSet;
    }


}
