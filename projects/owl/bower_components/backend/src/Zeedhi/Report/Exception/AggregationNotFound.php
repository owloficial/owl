<?php
namespace Zeedhi\Report\Exception;

/**
 * Description of AggregationNotFound
 *
 * @author tuliopinto
 */
class AggregationNotFound extends \RuntimeException{

    protected $message = 'Aggregation class not found. Be sure that class exists and its namespace was registered in zeedhi autoloader.';

}