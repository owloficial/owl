<?php
namespace Zeedhi\Report\DataSource\Aggregation;

use \Zeedhi\Report\DataSource\Aggregation\AbstractAggregation;

/*
 * This class will always assume that the field to be aggregated is the first one in the aggregationFields array.
 */
class Avg extends AbstractAggregation
{	
	private $countLine;
	private $sumValues;

	public function updateAggregator($row) 
	{
		$this->countLine++;
		$this->sumValues += $row[strtoupper($this->aggregationField)];
	}

	public function getValue() 
	{
		return $this->sumValues/$this->countLine;
	}

}