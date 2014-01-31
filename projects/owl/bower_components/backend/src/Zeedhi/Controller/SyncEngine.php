<?php
namespace Zeedhi\Controller;

use \Zeedhi\Data\Mongo;
use \Zeedhi\Application;

abstract class SyncEngine
{
	const ID_ASSOCIATION = "id_association";

	private $mongo;

	public function __construct() {
	}
	private function getMongo(){
		if($this->mongo == null){
			$this->mongo = new Mongo();	
		}
		return  $this->mongo;
	}
	public abstract function getServerId($params = array());

	public function getParentServerId($localParentKeys, $userId, $localId) 
	{
		$data = $this->getIdInAssociation($userId, $localParentKeys["TABLE"], $localParentKeys["PARENT_FIELD"]);
		return $data;
	}

	protected function getIdInAssociation($userId, $tableName, $columnName) 
	{
		$criteria = array (
			"USER_ID" => $userId,
			"TABLE"   => $tableName,
			"COLUMN"  => $columnName,
		);
		$result = $this->getMongo()->find(self::ID_ASSOCIATION, $criteria);
		return isset($result[0]) ? $result[0]["ID_SERVER"] : null;
	}

	protected function saveIdAssociation($userId, $tableName, $columnName, $localId, $serverId)
	{
		try {
			$obj = array(
				"USER_ID"     => $userId,
				"TABLE"       => $tableName,
				"COLUMN"      => $columnName,
				"ID_LOCAL"    => $localId,
				"ID_SERVER"   => $serverId,
			);
			$this->getMongo()->save(self::ID_ASSOCIATION, $obj);
		} catch (\MongoException $e) {
			throw $e;
		}
	}

}