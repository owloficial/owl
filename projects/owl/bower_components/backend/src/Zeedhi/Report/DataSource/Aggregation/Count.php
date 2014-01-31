<?php
namespace Zeedhi\Report\DataSource\Aggregation;

use \Zeedhi\Report\DataSource\Aggregation\AbstractAggregation;

class Count extends AbstractAggregation
{	

	private $count;

	public function updateAggregator($row) 
	{
		$this->count++;
	}

	public function getValue() 
	{
		return $this->count;
	}

}