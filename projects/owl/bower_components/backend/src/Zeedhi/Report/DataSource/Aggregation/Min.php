<?php
namespace Zeedhi\Report\DataSource\Aggregation;

use \Zeedhi\Report\DataSource\Aggregation\AbstractAggregation;

/*
 * This class will always assume that the field to be aggregated is the first one in the aggregationFields array.
 */
class Min extends AbstractAggregation
{	

	private $minValue;

	public function __construct($name, $dataSet, $aggregationFields) 
	{
		parent::__construct($name, $dataSet, $aggregationFields);
		$this->minValue = 99999999999999999999999999999999;
	}

	public function updateAggregator($row) 
	{
		$rowVal         = floatval($row[strtoupper($this->aggregationField)]);
		$this->minValue = $this->minValue < $rowVal ? $this->minValue : $rowVal;
	}

	public function getValue() 
	{
		return $this->minValue;
	}

}