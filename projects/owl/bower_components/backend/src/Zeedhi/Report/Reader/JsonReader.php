<?php
namespace Zeedhi\Report\Reader;

use \Zeedhi\Report\Datasource\ReportDataSource;
use \Zeedhi\Report\Report;
use \Zeedhi\Report\Band;
use \Zeedhi\Report\Group;
use \Zeedhi\Report\Field;

class JsonReader implements IReader
{

    private $config;
    private $dataset;


    public function __construct($config, $dataset) 
    {
        $this->config  = $config;
        $this->dataset = $dataset;
    }

    public function read() 
    {
        $reportDataSource = new ReportDataSource($this->config);
        $reportDataSource->build($this->dataset);		

        $report = new Report();
        $report->setName($this->config["name"]);
        $report->setTitle($this->config["label"]);
        $report->setVersion($this->config["version"]);

        // Construcao das bands
        $bands       = $this->config["bands"];
        $detailBands = array();
        foreach ($bands as $band) {
            if ($band["type"] === "D") {
                $temp          = $this->buildBand($band, $reportDataSource);
                $detailBands[] = $temp;
                $report->setNumColumns(count($temp->getFields()));
            } else if ($band["type"] === "S") {
                $groupAggregations = $reportDataSource->getAggregatedFields();
                $temp          = $this->getBand($band["name"], $reportDataSource, $groupAggregations);
                $detailBands[] = $temp;
            }
        }

        $groups = $this->buildGroups($this->config['groups'][0]['name'], $reportDataSource);
        $report->setGroups($groups);

        $report->setBands($detailBands);
        return $report;
    }

    private function buildBand($band, ReportDataSource $reportDataSource)
    {
        $builtBand = new Band();
        $builtBand->setName($band["name"]);
        $builtBand->setType($band["type"]);
        $builtBand->setFields($this->buildFields($band["fields"]));
        return $builtBand;
    }

    private function buildFields($fields) 
    {
        $builtFields = array();
        $configFields = $this->config["fields"];
        if ($fields) {
            foreach ($fields as $field) {
                foreach ($configFields as $fieldName => $configField) {
                    if ($fieldName === $field) {
                        $temp = new Field();
                        $temp->setName($fieldName);
                        $temp->setLabel($configField["label"]);

                        

                        if (isset($configField["formatter"]) && $configField["formatter"]) {
                            $formatterConfig = $configField["formatter"];
                            $temp->setFormatterClass($formatterConfig['class']);
                            $temp->setFormatterParams($formatterConfig['params']);
                        }
                        $builtFields[] = $temp;
                    }
                }
            }
        }

        return $builtFields;
    }

    private function buildGroups($groupName, ReportDataSource $reportDataSource) 
    {
        if ($groupName) {
            $groupData = $reportDataSource->getGroup($groupName);
            $groupConfig = $this->getGroupConfig($groupName);
            $groups = array();
            foreach ($groupData->getGroupIds() as $id) {
                $data              = $groupData->getGroupData($id);
                $groupAggregations = $groupData->getAggregationFields($id);
                $bandHeader        = $this->getBand($groupConfig["bandHeader"], $reportDataSource, $groupAggregations);
                $bandFooter        = $this->getBand($groupConfig["bandFooter"], $reportDataSource, $groupAggregations);
                $group = new Group();
                $group->setDataset($data);
                $group->setHeader($bandHeader);
                $group->setFooter($bandFooter);

                $groups[] = $group;
            }

            return $groups;
        }
    }

    private function getGroupConfig($groupName)
    {
        foreach ($this->config["groups"] as $groupConfig) {
            if ($groupConfig["name"] === $groupName) {
                return $groupConfig;
            }
        }

        return null;
    }

    private function getBand($bandName, $reportDataSource, $groupAggregations) 
    {
        $bandsConfig = $this->config["bands"];
        $band = null;
        foreach ($bandsConfig as $bandConfig) {
            if ($bandName === $bandConfig["name"]) {
                $band = $this->buildBand($bandConfig, $reportDataSource);
            }

            if ($band && $band->getFields()) {
                foreach ($band->getFields() as $field) {
                    foreach ($groupAggregations as $aggregation) {
                        if ($field->getName() === $aggregation->getName()) {
                            $field->setValue($aggregation->getValue());
                        }
                    }
                }
            }
        }

        return $band;
    }

}