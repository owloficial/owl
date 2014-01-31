<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Bundle\DoctrineBundle;

use Doctrine2\Common\EventManager;
use Doctrine2\DBAL\Configuration;
use Doctrine2\DBAL\DriverManager;
use Doctrine2\DBAL\Types\Type;

/**
 * Connection
 */
class ConnectionFactory
{
    private $typesConfig = array();
    private $initialized = false;

    /**
     * Construct.
     *
     * @param array $typesConfig
     */
    public function __construct(array $typesConfig)
    {
        $this->typesConfig = $typesConfig;
    }

    /**
     * Create a connection by name.
     *
     * @param array         $params
     * @param Configuration $config
     * @param EventManager  $eventManager
     *
     * @return Doctrine2\DBAL\Connection
     */
    public function createConnection(array $params, Configuration $config = null, EventManager $eventManager = null, array $mappingTypes = array())
    {
        if (!$this->initialized) {
            $this->initializeTypes();
            $this->initialized = true;
        }

        $connection = DriverManager::getConnection($params, $config, $eventManager);

        if (!empty($mappingTypes)) {
            $platform = $connection->getDatabasePlatform();
            foreach ($mappingTypes as $dbType => $doctrineType) {
                $platform->registerDoctrineTypeMapping($dbType, $doctrineType);
            }
        }

        return $connection;
    }

    private function initializeTypes()
    {
        foreach ($this->typesConfig as $type => $className) {
            if (Type::hasType($type)) {
                Type::overrideType($type, $className);
            } else {
                Type::addType($type, $className);
            }
        }
    }
}
