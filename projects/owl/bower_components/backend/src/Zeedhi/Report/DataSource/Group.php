<?php
namespace Zeedhi\Report\DataSource;

use \Zeedhi\Report\DataSource\Aggregation\AbstractAggregation;
use \Zeedhi\Report\DataSource\Exceptions\GroupKeyNotFoundException;

class Group 
{

    private $name;

    /*
     * @var array Identifies all columns used as key for each group.
     */
    private $groupByFields;

    /*
     * @var array This array contains all 
     */
    private $groupData;
    private $aggregationsPerGroup;
    private $aggregationFields;
    private $groupIds;
    private $reportFields;

    public function __construct($name, $groupByFields = array(), $fields = array()) 
    {
        $this->name                 = $name;
        $this->groupByFields        = $groupByFields;
        $this->reportFields         = $fields;
        $this->groupIds             = array();
        $this->groupData            = array();
        $this->aggregationFields    = array();
        $this->aggregationsPerGroup = array();
    }

    public function updateGroup($row) 
    {
        $key = $this->getGroupKey($row);
        $this->groupData[$key][] = $row;

        if (!isset($this->aggregationsPerGroup[$key])) {
            $this->groupIds[]                 = $key;
            $this->aggregationsPerGroup[$key] = $this->replicateAggregationFields();
        }

        foreach ($this->aggregationsPerGroup[$key] as $aggregationField) {
            $aggregationField->updateAggregator($row);
        }
    }

    public function addAggregationField(AbstractAggregation $aggregationField) 
    {
        $this->aggregationFields[] = $aggregationField;
    }

    public function getGroupIds() 
    {
        return $this->groupIds;
    }

    public function getAggregationFields($groupId)
    {
        return $this->aggregationsPerGroup[$groupId];
    }

    public function getName() 
    {
        return $this->name;
    }

    public function getGroupData($groupId)
    {
        return $this->groupData[$groupId];
    }

    private function getGroupKey($row) {
        $groupKey = "";
        foreach ($this->groupByFields as $groupByField) {
            $aggregationField = $this->reportFields[$groupByField]['aggregationField'];
            if (!isset($row[$aggregationField])) {
                throw new GroupKeyNotFoundException("Invalid group field $groupByField set.");
            }

            $groupKey .= $row[$aggregationField];
        }

        return $groupKey;
    }

    private function replicateAggregationFields() {
        $newArray = array();
        foreach ($this->aggregationFields as $key => $aggregationField) {
            $newArray[$key] = clone $aggregationField;
        }

        return $newArray;
    }

}