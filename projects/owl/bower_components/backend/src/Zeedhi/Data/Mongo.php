<?php
namespace Zeedhi\Data;

use \Zeedhi\Application;

class Mongo 
{

    private $db;

    public function __construct() 
    {
        try {
            $config = Application::getInstance()->getMongoConfig();
            $server = $config["server"];
            $port   = $config["port"];
            $dbname = $config["dbname"];
            $class = '\MongoClient';               
            if(!class_exists($class)){ 
                $class = '\Mongo'; 
            } 
            $mongo = new $class("mongodb://$server:$port");
            $this->db = $mongo->$dbname;
        } catch (\MongoConnectionException $e) {
            throw $e;
        }
    }

    public function find($collectionName, $criteria = array(), $sort = array(), $limit = 0)
    {
        try {
            $found = $this->db->$collectionName->find($criteria)->limit($limit);
            $found->sort($sort);

            $result = array();
            while ($doc = $found->getNext()) {
                $result[] = $doc;
            }

            return $result;
        } catch (\MongoConnectionException $e) {
            throw $e;
        }
    }

    public function save($collectionName, $object) 
    {
        try {
            $collection = $this->db->$collectionName;
            $collection->insert($object);
        } catch (\MongoException $e) {
            throw $e;
        }
    }

    public function update($collectionName, $criteria, $update, $upsert = false, $multi = false)
    {
        try {
            $collection = $this->db->$collectionName;
            $options = array(
                "upsert" => $upsert,
                "multi"  => $multi,
            );

            $collection->update($criteria, $update, $options);
        } catch (\MongoException $e) {
            throw $e;
        }
    }

    public function remove($collectionName, $criteria, $justOne = true) 
    {
        try {
            $collection = $this->db->$collectionName;
            $options    = array(
                "justOne" => $justOne,
            );

            $collection->remove($criteria, $options);
        } catch (\MongoException $e) {
            throw $e;
            
        }
    }

    public function drop($collectionName) 
    {
        try {
            $this->db->$collectionName->drop();
        } catch (\MongoException $e) {
            throw $e;
        }
    }

}