<?php
require 'bootstrap.php';
require 'doctrine_bootstrap.php';

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Helper\HelperSet;

$helperSet = new HelperSet(array(
    'db' => new ConnectionHelper($entity_manager->getConnection()),
    'em' => new EntityManagerHelper($entity_manager)
));


ConsoleRunner::run($helperSet);
/** orm:convert-mapping --force --from-database --namespace=Model\ annotation  ../src/ */