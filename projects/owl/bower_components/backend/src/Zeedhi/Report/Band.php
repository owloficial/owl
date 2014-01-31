<?php

namespace Zeedhi\Report;

class Band 
{

	private $name;
	private $type;
	private $fields;
	private $groups;

	public function getName() 
	{
		return $this->name;
	}

	public function setName($name) 
	{
		$this->name = $name;
	}

	public function getType() 
	{
		return $this->type;
	}

	public function setType($type) 
	{
		$this->type = $type;
	}

	public function getFields() 
	{
		return $this->fields;
	}

	public function setFields($fields) 
	{
		$this->fields = $fields;
	}

	public function getGroups() 
	{
		return $this->groups;
	}

	public function setGroups($groups) 
	{
		$this->groups = $groups;
	}
	
}