<?php

namespace Zeedhi\Report;

class Group 
{
	private $header;
	private $dataset;
	private $footer;

	public function getHeader() 
	{
		return $this->header;
	}

	public function setHeader($header) 
	{
		$this->header = $header;
	}

	public function getDataset() 
	{
		return $this->dataset;
	}

	public function setDataset($dataset) 
	{
		$this->dataset = $dataset;
	}

	public function getFooter() 
	{
		return $this->footer;
	}

	public function setFooter($footer) 
	{
		$this->footer = $footer;
	}

}