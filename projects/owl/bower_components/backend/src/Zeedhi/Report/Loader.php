<?php
namespace Zeedhi\Report;

/**
 * Description of Loader
 *
 * @author tuliopinto
 */
class Loader {

    private $application;

    private $datasetQueryLoader;

    /**
     * Constructor.
     * 
     * @param \Zeedhi\Application $application
     * @param \Zeedhi\Report\Loader\Dataset\DatasetLoadable $datasetQueryLoader
     */
    public function __construct(\Zeedhi\Application $application, Loader\Dataset\DatasetLoadable $datasetQueryLoader) {
        $this->application = $application;
        $this->datasetQueryLoader = $datasetQueryLoader;
    }

    /**
     * Build report from report name.
     * 
     * @param string $reportName
     * @param LogoConfig $logoConfig
     * 
     * @return string Generated html file path.
     */
    public function buildReport($reportName, LogoConfig $logoConfig) {
        $templatePath = SERVICE_PATH."/../../../../backend/src/Zeedhi/Report/templates";
        $cachePath = SERVICE_PATH."/../../../../backend/src/Zeedhi/Report/templates/cache";

        $targetFilePath = $this->createTargetFilePath($reportName);
//        $targetFileUrl = $this->createTargetFileUrl($reportName);
        $reportMetadata = $this->loadReportMetadata($reportName);
        $reportData = $this->loadReportData($reportMetadata);

        $reader = new \Zeedhi\Report\Reader\JsonReader($reportMetadata, $reportData);
        $reportBuilder = new \Zeedhi\Report\ReportBuilder($reader, $logoConfig, $templatePath, $cachePath, $targetFilePath);
        $reportBuilder->buildReport();
        return $targetFilePath;
    }

    /**
     * Create the needed connection to execute report query.
     * 
     * @return \Doctrine\DBAL\Connection
     */
/*    public function createNeededConnection() {
        $connectionId = 2;
        $entityManager = $this->getApplication()->getEntityManager();
        $connection = $entityManager->find('\\Model\\Datasource\\Entities\\Connection', $connectionId);

        $connectionParams = array(
            'host'     => $connection->getHost(),
            'port'     => $connection->getPort(),
            'dbname'   => $connection->getDbname(),
            'user'     => $connection->getUsername(),
            'password' => $connection->getPassword(),
            'driver'   => $connection->getDriver()
        );

        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, new \Doctrine\DBAL\Configuration());
        return $conn;
    }*/

    /**
     * Get report query from report name.
     * 
     * @param string $reportName
     * 
     * @return string
     */
/*    public function getReportQuery($reportName) {
        $entityManager = $this->getApplication()->getEntityManager();
        $widgetRepository = $entityManager->getRepository('\\Model\\Viewer\Entities\\Widget');
        $widgetEntity = $widgetRepository->findOneBy(array('name'=>$reportName));
        $sql = $widgetEntity->getDatasource()->getQuery();
        return $sql;
    }*/

    /**
     * Load report metadata from file a json file using report name.
     * 
     * @param string $reportName
     * 
     * @return array
     */
    public function loadReportMetadata($reportName) {
        $jsonFileName = strtolower($reportName).'.json';
        $jsonFilePath = SERVICE_PATH. "/../gen/reports/$jsonFileName";
        $reportMetadata  = json_decode(file_get_contents($jsonFilePath), true);
        return $reportMetadata['report'];
    }

    /**
     * Create target file path from report name.
     * 
     * @param string $reportName
     * 
     * @return string
     */
    public function createTargetFilePath($reportName) {
        $htmlFileName = strtolower($reportName).'.html';
        $targetFilePath = SERVICE_PATH."/../gen/reports/$htmlFileName";
        return $targetFilePath;
    }

    /**
     * Create target file url from report name.
     * 
     * @param string $reportName
     * 
     * @return string
     */
    public function createTargetFileUrl($reportName) {
        $htmlFileName = strtolower($reportName).'.html';
        return "../backend/gen/reports/$htmlFileName";
    }

    /**
     * Load report data from reportName.
     * 
     * @param string $reportName
     * 
     * @return array
     */
    public function loadReportData($reportMetadata) {
        $datasetQueryLoader = $this->getDatasetQueryLoader();
        $sql = $datasetQueryLoader->loadReportSql($reportMetadata);

        $entityManager = $this->getApplication()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('wpv.value')
            ->from('\Model\Viewer\Entities\WidgetPropertyValue', 'wpv')
            ->join('wpv.widget', 'w')
            ->join('wpv.templateProperty', 'tp')
            ->where('w.name = :widgetName')
            ->setParameter('widgetName', $reportMetadata['name']);
        
        $queryBuilderParams = clone $queryBuilder;
        $queryBuilderParams->andWhere('tp.name = :propertyName')
            ->setParameter('propertyName', 'sqlParams');
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();
        $connectionName = $result[0]['value'];

        $connectionRepository = $entityManager->getRepository('\Model\Datasource\Entities\Connection');
        $connectionEntity = $connectionRepository->findOneBy(array('name'=>$connectionName));

        $queryParams = $queryBuilderParams->getQuery();
        $resultParams = $queryParams->getResult();

        $params = array();
        if (!empty($resultParams) && $resultParams[0]['value'] && $temp = json_decode($resultParams[0]['value'], true)) {
            $params = $temp;
        }


        $conn = $this->createConnectionFromEntity($connectionEntity);
        $data = $conn->fetchAll($sql, $params);
        return $data;
    }

    /**
     * Create a connection object from a connection entity;
     * 
     * @param \Model\Datasource\Entities\Connection $connectionEntity
     * 
     * @return \Doctrine\DBAL\Connection
     */
    private function createConnectionFromEntity(\Model\Datasource\Entities\Connection $connectionEntity) {
        $connectionParams = array(
            'host'     => $connectionEntity->getHost(),
            'port'     => $connectionEntity->getPort(),
            'dbname'   => $connectionEntity->getDbname(),
            'user'     => $connectionEntity->getUsername(),
            'password' => $connectionEntity->getPassword(),
            'driver'   => $connectionEntity->getDriver()
        );

        $eventManager = new \Doctrine\Common\EventManager();
        $eventManager->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\OracleSessionInit());

        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, new \Doctrine\DBAL\Configuration(), $eventManager);
        return $conn;
    }

    /**
     * 
     * 
     * @param string $reportName
     * 
     * @return object
     */
    public function createReportDataSourceObject($dataSourceName) {
        $namespace = '\\Generated\\Datasource\\';
        $className = \Doctrine\Common\Util\Inflector::classify(strtolower($dataSourceName));
        $fullClassName = $namespace . $className;
        return $fullClassName;
    }

    /**
     * 
     * @return \Zeedhi\Application
     */
    public function getApplication() {
        return $this->application;
    }

    public function getDatasetQueryLoader() {
        return $this->datasetQueryLoader;
    }

}