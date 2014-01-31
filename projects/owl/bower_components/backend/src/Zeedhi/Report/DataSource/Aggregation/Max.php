<?php
namespace Zeedhi\Report\DataSource\Aggregation;

use \Zeedhi\Report\DataSource\Aggregation\AbstractAggregation;

/*
 * This class will always assume that the field to be aggregated is the first one in the aggregationFields array.
 */
class Max extends AbstractAggregation
{	

	private $maxValue;

	public function __construct($name, $dataSet, $aggregationFields) 
	{
		parent::__construct($name, $dataSet, $aggregationFields);
		$this->maxValue = -99999999999999999999999999999999;
	}

	public function updateAggregator($row) 
	{
		$rowVal         = floatval($row[strtoupper($this->aggregationField)]);
		$this->maxValue = $this->maxValue > $rowVal ? $this->maxValue : $rowVal;
	}

	public function getValue() 
	{
		return $this->maxValue;
	}

}