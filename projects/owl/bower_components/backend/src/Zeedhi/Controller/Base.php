<?php
namespace Zeedhi\Controller;

abstract class Base{

    /**@var \Zeedhi\Application*/
    private $app = null;
    private $dataSource = null;

    /**
    * @param \Zeedhi\Application $app
    */
    public function __construct($app){
        $this->app = $app;
    }

    /**@return \Zeedhi\Application*/
    public function getApplication(){
        return $this->app;
    }

    public function getDataSource($request){
        if($this->dataSource == null){            
            if ($request instanceof \Zeedhi\Request\EventRequestDataSet) {
                $this->dataSource = new \Zeedhi\Data\DataSource($this->getDataSourceConfig(), $request->getDataset());
            } else if($request instanceof \Zeedhi\Request\EventRequestRow){
                $this->dataSource = new \Zeedhi\Data\DataSource($this->getDataSourceConfig(), array($request->getRow()));
            }else{
                $this->dataSource = new \Zeedhi\Data\DataSource($this->getDataSourceConfig());
            }
        }
        return $this->dataSource;
    }

    public function getDataSourceConfig(){

        return $this->getApplication()->getDataSourceConfig($this->dataSourceName);
    }

}