<?php
namespace Zeedhi\Request;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 11:59
 * To change this template use File | Settings | File Templates.
 */
class EventWizard extends \Zeedhi\Request\EventRequest
{

    private  $dataSets;

    public function setDataSets($dataSets)
    {
        $this->dataSets = $dataSets;
    }

    public function getDataSets()
    {
        return $this->dataSets;
    }
}
