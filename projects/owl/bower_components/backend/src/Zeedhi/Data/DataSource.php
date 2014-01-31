<?php
namespace Zeedhi\Data;

/**
 * 
 */
class DataSource{

    private $rows;

    private $properties;
    
    public function __construct($datasourceConfig, $dataSet = null){
        $this->rows = $dataSet;
        $this->tableName = $datasourceConfig['tableName'];
        $this->properties = $datasourceConfig['columns'];
        $this->key = $datasourceConfig['key'];
        $this->localKey = $datasourceConfig['localKey'];
        if(isset($datasourceConfig['parentKeys'])){
            $this->localParentKeys = $datasourceConfig['parentKeys'];
        }else{
            $this->localParentKeys = array();
        }
            
    }
    
    public function getLocalParentKeys(){
        return $this->localParentKeys;
    }

    public function getLocalKey(){
        return $this->localKey;
    }
    public function getRows(){
        return $this->rows;
    }

    public function getId($row){
        $pkValues = array();
        foreach($this->key as $column) {
            $pkValues[$column] = $row[$column];
        }

        return $pkValues;
    }

    public function getProperties(){
        return $this->properties;
    }

    public function getTableName(){
        return $this->tableName;
    }

}