<?php
namespace Zeedhi\Report\DataSource\Aggregation;

use \Zeedhi\Report\DataSource\Aggregation\AbstractAggregation;

/**
 * This class was meant to be just a helper, to be used as the index of a grouped data set. Different from the other 
 * Aggregation classes, this one just return the value received as parameter (what means this one doesn't aggregate data,
 * just index what as grouped).
 * 
 * This class will always assume that the field to be aggregated is the first one in the aggregationField array.
 */
class Constant extends AbstractAggregation {

    public function __construct($name, $aggregationFields, $value = null) {
        parent::__construct($name, $aggregationFields);
        $this->value = $value;
    }

    public function updateAggregator($row) {
        if (!$this->value) {
            $this->value = $row[$this->aggregationField];
        }
    }

    public function getValue() {
        return $this->value;
    }

}