<?php
namespace Zeedhi\Request;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 11:39
 * To change this template use File | Settings | File Templates.
 */
class FilterDataRequest extends \Zeedhi\Request\Request
{

    private $filter;
    private $page = 1;

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPage($page) {
        if($page)
            $this->page = $page;
    }

}