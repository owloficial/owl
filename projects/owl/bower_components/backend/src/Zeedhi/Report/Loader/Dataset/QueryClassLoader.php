<?php
namespace Zeedhi\Report\Loader\Dataset;

/**
 * Description of Loader
 *
 * @author tuliopinto
 */
class QueryClassLoader implements DatasetLoadable {

    private $application;

    /**
     * 
     * @param type $application
     */
    public function __construct($application) {
        $this->application = $application;
    }

    /**
     * Load report data from reportName.
     * 
     * @param string $reportName
     * 
     * @return array
     */
    public function loadReportSql($reportMetadata) {
        $dataSource = $this->createReportDataSourceObject($reportMetadata['dataSourceName']);
        return $dataSource::QUERY;
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

}