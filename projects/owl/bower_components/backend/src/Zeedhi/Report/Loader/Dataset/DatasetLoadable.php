<?php
namespace Zeedhi\Report\Loader\Dataset;

/**
 *
 * @author tuliopinto
 */
interface DatasetLoadable {

    /**
     * Load report sql.
     * 
     * @param type $reportMetadata
     *
     * @return string
     */
    public function loadReportSql($reportMetadata);

}