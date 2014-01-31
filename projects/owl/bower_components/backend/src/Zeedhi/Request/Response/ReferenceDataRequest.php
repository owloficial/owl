<?php
/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 11:57
 * To change this template use File | Settings | File Templates.
 */
class ReferenceDataRequest extends \Zeedhi\Request\Request
{

    private $filter;


    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }
}
