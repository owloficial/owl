<?php
require "..\..\..\..\libs\SplClassLoader.php";
$loader = new SplClassLoader('Zeedhi', realpath(__DIR__."/../../.."));
$loader->register();

$response = new \Zeedhi\HTTP\Response\JSON(array("foo"=>array("bar","baz")), \Zeedhi\HTTP\Response::STATUS_CODE_NOT_IMPLEMENTED);
$response->send();

$response = new \Zeedhi\HTTP\Response\JSON();
$response->setContent(array("foo"=>array("bar","baz")));
$response->setStatusCode(\Zeedhi\HTTP\Response::STATUS_CODE_NOT_IMPLEMENTED);
//$response->send();
