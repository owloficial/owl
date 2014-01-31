<?php
namespace Zeedhi\Report\Loader\Dataset;

/**
 * Description of Loader
 *
 * @author tuliopinto
 */
class QueryMetadataLoader implements DatasetLoadable {

    private $application;

    /**
     * 
     * @param type $application
     */
    public function __construct($application) {
        $this->application = $application;
    }

    /**
     * Get report query from report name.
     * 
     * @param string $reportName
     * 
     * @return string
     */
    public function loadReportSql($reportMetadata) {
        $entityManager = $this->getApplication()->getEntityManager();
        $widgetRepository = $entityManager->getRepository('\\Model\\Viewer\Entities\\Widget');
        $widgetEntity = $widgetRepository->findOneBy(array('name'=>$reportMetadata['name']));
        $sql = $widgetEntity->getDatasource()->getQuery();
        return $sql;
    }

    /**
     * 
     * @return \Zeedhi\Application
     */
    public function getApplication() {
        return $this->application;
    }

}