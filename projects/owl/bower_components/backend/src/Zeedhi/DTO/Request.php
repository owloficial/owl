<?php
namespace Zeedhi\DTO;

class Request {
	private $header;

	public function getHeader() 
	{
		return $this->header;
	}

	public function setHeader($header) 
	{
		$this->header = $header;
	}

}