<?php

namespace Zeedhi\Data;

use \Zeedhi\Application;

class Procedure 
{

	private $name;
	private $params;

	public function __construct($name, $params = array()) 
	{
		$this->name = $name;
		$this->params = $params;
	}

	public function addParam(Procedure\Param $param) 
	{
		$this->params[] = $param;
	}

	public function call() 
	{
		$procedureString = $this->buildString();
		$connection = Application::getInstance()->getDataSourceManager()->getEntityConnection();
		$statement = $connection->prepare($procedureString);		
		$return = array();
		foreach ($this->params as $param) {
			if ($param->isOutput()) {
				$return[$param->getName()] = $param->getPreparedValue();
				$statement->bindParam($param->getParamAlias(), $return[$param->getName()], $param->getType(), $param->getLength());
			} else {
				$statement->bindParam($param->getParamAlias(), $param->getValue());
			}
		}

		if ($statement->execute()) {
			return $return;
		}

		return false;
	}

	public function buildString() {
		$string = "CALL {$this->name} (";
        $first = true;
        /** @var $param Procedure\Param */
        foreach($this->params as $param) {
            if ($first) $first = false;
            else $string .= ", ";
            $string .= $param->getParamAlias();
        }

        $string .= ")";
        return $string;
	}

}