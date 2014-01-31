<?php
$entitiesPath = realpath(__DIR__."/../src/");
$isDevMode = true;
$entity_metadata_config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array($entitiesPath), $isDevMode);

$connection_params = array(
    'driver'    => 'oci8',
    'user'      => 'APRESENTACAO_DYMAN',
    'password'  => 'APRESENTACAO_DYMAN',
    'host'      => '192.168.120.75',
    'port'      => '1521',
    'dbname'    => 'xe',
);

$entity_manager = \Doctrine\ORM\EntityManager::create($connection_params, $entity_metadata_config);
