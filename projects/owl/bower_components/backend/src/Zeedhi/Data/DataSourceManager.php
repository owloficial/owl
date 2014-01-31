<?php
namespace Zeedhi\Data;

interface DataSourceManager{
	

	function persist(\Zeedhi\Data\DataSource $datasource);

	function delete(\Zeedhi\Data\DataSource $datasource);

	function findBy(\Zeedhi\Data\DataSource $datasource, $filter, $page);

}