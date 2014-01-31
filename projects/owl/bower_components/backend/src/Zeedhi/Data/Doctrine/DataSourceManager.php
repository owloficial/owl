<?php
namespace Zeedhi\Data\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use \Zeedhi\Application;

class DataSourceManager implements \Zeedhi\Data\DataSourceManager {

    /** @var EntityManager */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * 
     * @param \Zeedhi\Data\DataSource $datasource
     */
    public function persist(\Zeedhi\Data\DataSource $datasource, $requestHeaders = null) {
        $className     = $this->classify($datasource->getTableName());
        $classMetaData = $this->entityManager->getClassMetadata($className);
        $typeValidator = new TypeValidator($classMetaData);

        if ($datasource->getRows()) {
            foreach ($datasource->getRows() as $row) {
                $tableName = $datasource->getTableName();
                $localKeys = $datasource->getLocalKey();
                $localParentKeys = $datasource->getLocalParentKeys();
                $row = $this->prepareLocalKeys($row, $localKeys, $localParentKeys, $requestHeaders, $tableName, $this->getUserId());
                $pkValues = $this->prepareIdKeys($datasource->getId($row));
                $entity   = $this->findOrNew($className, $pkValues);
                foreach ($datasource->getProperties() as $name) {
                    if (isset($row[$name])){
                        $value  = $typeValidator->getTypifiedField($name, $row[$name]);
                        $method = $this->setfy($name);
                        $entity->$method($value);
                    }
                }
                $this->entityManager->persist($entity);
            }
        }
        
        $this->entityManager->flush();
    }

    private function prepareLocalKeys($row, $localKeys, $localParentKeys, $headers, $tableName, $userId){
        $keys = array();
        if(isset($row['__createdLocal']) && $row['__createdLocal']){
            $syncEngine = Application::getInstance()->getSyncEngine();
            if ((bool)$syncEngine === false) {
                throw new \Exception("There is no Sync Engine created.");
            }

            foreach ($localKeys as $key => $name ) {
                
                $idLocal = $row[$name];
                $params  = array(
                    "TABLE_NAME"  => $tableName,
                    "COLUMN_NAME" => $name,
                    "LOCAL_ID"    => $idLocal,
                    "USER_ID"     => $userId,
                    "HEADER"       => $headers,
                );
                $row[$name] = $syncEngine->getServerId($params);
            }

            if ($localParentKeys) {
                foreach ($localParentKeys as $key) {
                    $row[$key["FIELD"]] = $syncEngine->getParentServerId($key, $userId, $row[$key["FIELD"]]);
                }
            }
        }

        return $row;
    }
    private function prepareIdKeys($pkValues) {
        $pk = array();
        foreach ($pkValues as $key=>$value) {
            $pk[strtolower($key)] = $value;
        }

        return $pk;
    }

    /**
     * @param \Zeedhi\Data\DataSource $datasource
     */
    public function delete(\Zeedhi\Data\DataSource $datasource) {
        $className = $this->classify($datasource->getTableName());
        foreach ($datasource->getRows() as $row) {
            $pkValues = $this->processFilter($datasource->getId($row));
            $entity = $this->entityManager->find($className, $pkValues);
            $this->entityManager->remove($entity);
        }
        $this->entityManager->flush();        
    }

    public function entityToRow(\Zeedhi\Data\DataSource $dataSource, $entity){
        $row = array();
        if ($dataSource->getProperties()) {
            foreach ($dataSource->getProperties() as $name) {
                $method = $this->getfy($name);
                if(method_exists($entity, $method)) {
                    $value = $entity->$method();
                } else {
                    $value = $this->traverseEntityByMethod($name, $entity);
                }
                
                if ($value instanceof \DateTime) {
                    $value = $value->format("d/m/Y H:i:s");
                }
                $row[$name] = $value;
            }
        }

        return $row;  
    }
    
    //[Marcelo Pedras] - Medida paleativa para pegar propriedades em entidades relacionadas
    private function traverseEntityByMethod($property, $entity) {
        
        $property = $this->propertify($property);
        if(preg_match("/Id$/", $property)) {
            $entityProperty = preg_replace("/Id$/", "", $property);            
            $getEntityMethod = $this->getfy($entityProperty);
            $nextEntity = $entity->$getEntityMethod();
            $nextEntityMethod = $this->getfy($property);           
            return $nextEntity->$nextEntityMethod();
        }
    }

    public function entitiesToRow(\Zeedhi\Data\DataSource $dataSource, $entities){
        $rows = array();
        foreach($entities as $entity){
            $rows[] = $this->entityToRow($dataSource, $entity);
        } 
        return $rows;       
    }
    public function findBySQL(\Zeedhi\Data\DataSource $dataSource, $entity, $alias, $sql) {
        $entityManager = $this->entityManager;
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata($entity, $alias);
        $query = $entityManager->createNativeQuery($sql, $rsm);
        $data = $this->entitiesToRow($dataSource, $query->getResult());
        return $data;
    }
    public function findBy(\Zeedhi\Data\DataSource $dataSource, $filterDefinitions, $page) {
        $className = $this->classify($dataSource->getTableName());        
        $filterDefinitions = $this->processFilter($filterDefinitions, $className);
        $criteria = $this->createCriteria($filterDefinitions);        
        $entities = $this->findByEntityManager($className, $criteria, $page);
        $rows = $this->entitiesToRow($dataSource, $entities);
        return $rows;
    }

    public function findByEntityManager($className, $criteria, $page) {
        $criteria->setMaxResults(10000)->setFirstResult(10000 * ($page - 1));
        $repository = $this->entityManager->getRepository($className);        
        $result = $repository->matching($criteria)->toArray();
        return $result;
    }    

    /**
     * @param array $filterDefinitions
     * @return \Doctrine\Common\Collections\Criteria
     */
    private function createCriteria($filterDefinitions = null) {
        $criteria = new \Doctrine\Common\Collections\Criteria();
        if($filterDefinitions) {
            $criteria->where($this->createWhereExpression($filterDefinitions[0]));
           
            unset($filterDefinitions[0]);
            foreach ($filterDefinitions as $filterDefinition) {
                if(isset($filterDefinition['condition']) && strtolower($filterDefinition['condition']) === 'or') {
                    $criteria->orWhere($this->createWhereExpression($filterDefinition));
                } else{
                    $criteria->andWhere($this->createWhereExpression($filterDefinition));
                }
            }
        }

        return $criteria;
    }
    
    private function createWhereExpression($filterDefinition) {
        $expr = new \Doctrine\Common\Collections\Expr\Comparison(
            $filterDefinition['name'],
            $filterDefinition['operator'],
            $filterDefinition['value']
        );

        return $expr;
    }

    private function processFilter($filterDefinitions, $className = null) {       
       
        if ($filterDefinitions) {
            
            /**
             * TODO - Marcelo Pedras - Discutir uma maneira melhor para melhorar o mapeamento datasource x doctrine             
             * Essa verificação é necessária para manter a compatibilidade do processFilter original, que não
             * tinha o parâmetro className. Este método pega o nome correto dos atributos de relacionamento da entidade.
             */
             
            if($className) {                
                $dataSourceFieldsName = $this->getAssociationMappingFieldsName($className);                
            }
            
            $filterNames = array();
        
            foreach($filterDefinitions as $key => $filterDefinition) {
                $filterNames[$key] = $filterDefinition['name'];
            }
            
            foreach ($filterDefinitions as &$filterDefinition) {
                $filterDefinition['name'] = $this->propertify($filterDefinition['name']);
            }
            
            //Marcelo Pedras - Este método muda o nome dos atributos do datasource para que reflita o nome correto esperado pelo doctrine.
            if($className) {
                $this->replaceFilterDefinitionsNames($dataSourceFieldsName, $filterNames, $filterDefinitions);
            }
        }

        return $filterDefinitions;
    }
    
    /**
     * Este método retorna um array com o nome das propriedades que compoem os relacinamentos da entidade.
     * A chave do array é o nome do propriedade que representa o nome do relacionamente na entidade pai. 
     */
    private function getAssociationMappingFieldsName($className) {
        $metaData = $this->entityManager->getClassMetadata($className);
        $fieldsMapping = $metaData->getAssociationMappings();
        $dataSourceFieldsName = array();
        foreach($fieldsMapping as $fieldMapping) {
            $entityFieldName = $fieldMapping['fieldName'];
            $dataSourceFieldsName[$entityFieldName] = $fieldMapping['sourceToTargetKeyColumns'];
        }
        
        return $dataSourceFieldsName;
    }
    
    /**
     * Este método muda o nome dos filterDefinitions para refletir as propriedades que de fato existem na entidade.      
     */
    private function replaceFilterDefinitionsNames($dataSourceFieldsName, $filterNames, &$filterDefinitions) {
        
        foreach($dataSourceFieldsName as $entityFieldName => $dataSourceFieldsName) {
            foreach($filterNames as $key => $filterName) {
                if(array_key_exists($filterName, $dataSourceFieldsName)) {
                    $filterDefinitions[$key]['name'] = $entityFieldName;                            
                }
            }
        }
    }

    protected function classify($tableName) {
        return "Model\\".\Doctrine\Common\Util\Inflector::classify(strtolower($tableName));
    }

    private function propertify($name) {
        return \Doctrine\Common\Util\Inflector::camelize(strtolower($name));
    }

    private function getfy($name) {
        return 'get'.ucfirst($this->propertify($name));
    }

    private function setfy($name) {
        return 'set'.ucfirst($this->propertify($name));
    }

    public function findByColumns($className, $array){
        $repository = $this->entityManager->getRepository($className);
        $result = $repository->findBy($array);
        return $result;        
    }
    /**
     * @param $className
     * @param $pkValues
     * @return mixed
     */
    public function findOrNew($className, $pkValues)
    {
        $entity = $this->entityManager->find($className, $pkValues);
        if(!$entity) {
            $entity = new $className();
        }

        return $entity;
    }

    /**
     * @param $page
     * @param $className
     * @param \Doctrine\Common\Collections\Criteria $criteria
     * @return mixed
     */
    public function findByCriteria($page, $className, \Doctrine\Common\Collections\Criteria $criteria)
    {
        $criteria->setMaxResults(300)->setFirstResult(300 * ($page - 1));
        $repository = $this->entityManager->getRepository($className);
        $result = $repository->matching($criteria)->toArray();
        return $result;
    }

    public function fetchAll($sql, $params = array()) 
    {
        try {
            return $this->entityManager->getConnection()->fetchAll($sql, $params);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function fetchRow($sql, $params = array()) 
    {
        try {
            $data = $this->entityManager->getConnection()->fetchAll($sql, $params);
            return isset($data[0]) ? $data[0] : null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the device's id (the id of the device used to manipulate this system).
     */
    public function getUserId() 
    {   
        $headers = ''; 
        foreach ($_SERVER as $name => $value)  { 
            if (substr($name, 0, 5) == 'HTTP_') { 
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            } 
        } 

        return $headers["User-Id"];
    }

    /*
     * @todo Verify where should I create this method.
     */
    public function getEntityConnection() 
    {
        return $this->entityManager->getConnection();
    }


}