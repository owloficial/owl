<?php
namespace Zeedhi\Report\DataSource\Aggregation;

use \Zeedhi\Report\DataSource\Aggregation\AbstractAggregation;

/*
 * This class will always assume that the field to be aggregated is the first one in the aggregationFields array.
 */
class Sum extends AbstractAggregation
{	

	private $sum;

	public function updateAggregator($row) 
	{
		$this->sum += floatval($row[strtoupper($this->aggregationField)]);
	}

	public function getValue() 
	{
		return $this->sum;
	}

}