<?php
namespace Zeedhi\Controller;

use \Zeedhi\Application;

class DefaultSyncEngine extends SyncEngine 
{

	public function getServerId($params = array())
	{
		$tableName  = $params["TABLE_NAME"];
		$columnName = $params["COLUMN_NAME"];
		$localId    = $params["LOCAL_ID"];
		$userId     = $params["USER_ID"];

		try {
			$serverId = $this->getIdInAssociation($userId, $tableName, $columnName);
			if ((bool)$serverId === false) {
				$serverId = $this->getNextId($tableName, $columnName);
				$this->saveIdAssociation($userId, $tableName, $columnName, $localId, $serverId);
			}

			return $serverId;
		} catch (\Exception $e) {
			throw $e;
		}

		return $serverId;
	}

	private function getNextId($tableName, $columnName) 
	{
		try {
			$sql  = "SELECT MAX($columnName) + 1 SERVER_ID FROM $tableName";
			$data = Application::getInstance()->getDataSourceManager()->fetchRow($sql);
			return is_null($data["SERVER_ID"]) ? 1 : $data["SERVER_ID"];
		} catch (\Exception $e) {
			throw $e;
		}
	}

}