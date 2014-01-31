<?php
namespace Zeedhi\Report\DataSource;

use \Zeedhi\Report\DataSource\Group;
use \Zeedhi\Report\DataSource\Aggregation\AbstractAggregation;
use \Zeedhi\Report\DataSource\Aggregation\AggregationFactory;

class ReportDataSource
{

	/** 
	 * @var array<\Zeedhi\Report\DataSource\Aggregation\AbstractAggregation>
	 */
	private $aggregatedFields = array();

	/** 
	 * @var array<\Zeedhi\Report\DataSource\Group>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
	 */
	private $groups;


	public function __construct($reportConfig)
	{
		$this->buildReportGroups($reportConfig);
		$this->buildReportAggregations($reportConfig);
	}

	public function build($dataset) 
	{
		foreach ($dataset as $row) {
			foreach ($this->groups as $group) {
				$group->updateGroup($row);
			}
			
			foreach ($this->aggregatedFields as $field) {
				$field->updateAggregator($row);
			}
		}
	}


	public function setAggregatedFields($aggregatedFields = array()) 
	{
		$this->aggregatedFields = $aggregatedFields;
	}


	public function getAggregatedFields() 
	{
		return $this->aggregatedFields;
	}


	public function addAggregatedField(AbstractAggregation $aggregatedFields) 
	{
		$this->aggregatedFields[] = $aggregatedFields;
	}


	public function getAgregatedField($name) 
	{
		$chosen = null;
		foreach ($this->aggregatedFields() as $field) {
			if ($field->getName() === $name) {
				$chosen = $field;
				break;
			}
		}

		return $chosen;
	}


	public function setGroups($groups = array()) 
	{
		$this->groups = $groups;
	}


	public function getGroups() 
	{
		return $this->groups;
	}


	public function addGroup(Group $group) 
	{
		$this->groups[] = $group;
	}


	public function getGroup($name) 
	{
		$chosenGroup = null;
		foreach ($this->getGroups() as $group) {
			if ($group->getName() === $name) {
				$chosenGroup = $group;
				break;
			}
		}

		return $chosenGroup;
	}


	private function buildReportGroups($reportConfig = array()) {
            $groupConfig = $reportConfig["groups"];
            foreach ($groupConfig as $config) {
                $group = new Group($config["name"], $config["fields"], $reportConfig['fields']);
                $this->buildGroupAggregations($group, $reportConfig);
                $this->addGroup($group);
            }
	}

	private function buildGroupAggregations(Group $group, $reportConfig) 
	{
		foreach ($reportConfig["fields"] as $fieldName => $field) {
			if (isset($field["hasGroup"]) && $field["hasGroup"] === true) {
				$aggregation = AggregationFactory::factory (
					$fieldName, $field["aggregationField"], $field["aggregationType"]
				);

				$group->addAggregationField($aggregation);
			}
		}
	}

	private function buildReportAggregations($reportConfig) 
	{
		foreach ($reportConfig["bands"] as $bandConfig) {
                    if($bandConfig['type'] === 'S') {
                        foreach ($bandConfig['fields'] as $fieldName){
                            $config = $reportConfig['fields'][$fieldName];
                            $aggregation = AggregationFactory::factory (
					$fieldName, $config["aggregationField"], $config["aggregationType"]
				);

                            $this->addAggregatedField($aggregation);
                        }
                    }
                }
	}

}