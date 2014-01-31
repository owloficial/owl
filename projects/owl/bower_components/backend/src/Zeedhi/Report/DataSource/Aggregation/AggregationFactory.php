<?php
namespace Zeedhi\Report\DataSource\Aggregation;

use \Zeedhi\Report\Exception\AggregationNotFound;

class AggregationFactory{

    /**
     * Create a aggregation class object.
     * 
     * @param string $name
     * @param string $aggregationField
     * @param string $type
     * 
     * @return \Zeedhi\Report\DataSource\Aggregation\type
     * 
     * @throws AggregationNotFound
     */
    public function factory($name, $aggregationField, $type) {
        $aggregation = null;
        if (class_exists($type)) {
            $aggregation = new $type($name, $aggregationField);
        } else {
            throw new AggregationNotFound("Aggregation class $type not found. Be sure that class exists and its namespace was registered in zeedhi autoloader.");
        }

        return $aggregation;
    }

}