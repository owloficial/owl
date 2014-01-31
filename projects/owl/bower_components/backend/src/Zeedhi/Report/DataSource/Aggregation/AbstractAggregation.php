<?php
namespace Zeedhi\Report\DataSource\Aggregation;

abstract class AbstractAggregation
{

	protected $name;
	protected $aggregationField;
	protected $value;

	public function __construct($name, $aggregationField) 
	{
		$this->name             = $name;
		$this->aggregationField = $aggregationField;
		$this->value            = 0;
	}

	public function getName() 
	{
		return $this->name;
	}


	public function addRow($row) 
	{
		$this->updateAggregator($row);
	}

	public abstract function updateAggregator($row);

	public abstract function getValue();
	
}