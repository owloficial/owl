<?php
namespace Temp;

class DataSourceManager extends \Zeedhi\Data\Doctrine\DataSourceManager {

    private $submodelMapping = array(
        'PACIENTE' => 'Model\\Entities\\Paciente',
    );

    /**
     * Getr className by tableName
     *
     * @param string $tableName
     *
     * @return string
     */
    protected function classify($tableName) {
        return $this->submodelMapping[$tableName];
    }

}