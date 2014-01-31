<?php
namespace Zeedhi\Request;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 11:08
 * To change this template use File | Settings | File Templates.
 */
class RequestFactory
{

    const EVENT_CELL                          = "Cell";
    const EVENT_EMPTY                         = "Empty";
    const EVENT_REQUEST                       = "";
    const EVENT_REQUEST_CHECKROW              = "CheckRow";
    const EVENT_REQUEST_DATASET               = "DataSet";
    const EVENT_REQUEST_ROW                   = "Row";
    const EVENT_WIZARD                        = "Wizard";
    const FILTER_DATA_REQUEST                 = "FilterData";
    const REFERENCE_AUTOCOMPLETE_DATA_REQUEST = "AutoCompleteData";

    public static function factory($requestType, $request) {
        switch ($requestType) {
            case self::EVENT_CELL:
                $eventRequest = new EventCell();
                $eventRequest->setValue($request["value"]);
                return $eventRequest;
            case self::EVENT_EMPTY:
                $eventRequest = new EventEmpty();
                return $eventRequest;
//            case self::EVENT_REQUEST:
//                return new EventRequest();
            case self::EVENT_REQUEST_CHECKROW:
                $eventRequest = new EventRequestCheckRow();
                $eventRequest->setRow($request["row"]);
                return $eventRequest;
            case self::EVENT_REQUEST_DATASET:
                $eventRequest = new EventRequestDataSet();
                if (isset($request["dataset"])) {
                    $eventRequest->setDataSet($request["dataset"]);
                } else {
                    $eventRequest->setDataSet(null);
                }

                return $eventRequest;
            case self::EVENT_REQUEST_ROW;
                $eventRequest = new EventRequestRow();
                $eventRequest->setRow($request["row"]);
                return $eventRequest;
            case self::EVENT_WIZARD:
                $eventRequest = new EventWizard();
                $eventRequest->setDataSet($request["dataset"]);
                return $eventRequest;
            case self::FILTER_DATA_REQUEST:
                $eventRequest = new FilterDataRequest();
                if(isset($request["filter"])) {
                    $eventRequest->setFilter($request["filter"]);
                }

                if(isset($request["page"])) {
                    $eventRequest->setPage($request["page"]);
                }
                $eventRequest->setParams($request["params"]);
                return $eventRequest;
            case self::REFERENCE_AUTOCOMPLETE_DATA_REQUEST:
                $eventRequest = new ReferenceAutoCompleteDataRequest();
                $eventRequest->setIn($request["in"]);
                $eventRequest->setValue($request["value"]);
                return $eventRequest;
            default:
                throw new \Exception("Invalid option ");
        }
    }

}
