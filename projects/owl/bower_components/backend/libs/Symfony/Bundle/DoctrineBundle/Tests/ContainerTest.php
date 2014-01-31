<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\DoctrineBundle\Tests;

use Doctrine2\DBAL\Types\Type;

class ContainerTest extends TestCase
{
    public function testContainer()
    {
        $container = $this->createYamlBundleTestContainer();
        $this->assertInstanceOf('Doctrine2\DBAL\Logging\DebugStack', $container->get('doctrine.dbal.logger.debug'));
        $this->assertInstanceOf('Doctrine2\DBAL\Logging\DebugStack', $container->get('doctrine.dbal.logger'));
        $this->assertInstanceOf('Symfony\Bridge\Doctrine2\DataCollector\DoctrineDataCollector', $container->get('data_collector.doctrine'));
        $this->assertInstanceOf('Doctrine2\DBAL\Configuration', $container->get('doctrine.dbal.default_connection.configuration'));
        $this->assertInstanceOf('Doctrine2\Common\EventManager', $container->get('doctrine.dbal.default_connection.event_manager'));
        $this->assertInstanceOf('Doctrine2\DBAL\Connection', $container->get('doctrine.dbal.default_connection'));
        $this->assertInstanceOf('Doctrine2\Common\Annotations\Reader', $container->get('doctrine.orm.metadata.annotation_reader'));
        $this->assertInstanceOf('Doctrine2\ORM\Configuration', $container->get('doctrine.orm.default_configuration'));
        $this->assertInstanceOf('Doctrine2\ORM\Mapping\Driver\DriverChain', $container->get('doctrine.orm.default_metadata_driver'));
        $this->assertInstanceOf('Doctrine2\Common\Cache\ArrayCache', $container->get('doctrine.orm.default_metadata_cache'));
        $this->assertInstanceOf('Doctrine2\Common\Cache\ArrayCache', $container->get('doctrine.orm.default_query_cache'));
        $this->assertInstanceOf('Doctrine2\Common\Cache\ArrayCache', $container->get('doctrine.orm.default_result_cache'));
        $this->assertInstanceOf('Doctrine2\ORM\EntityManager', $container->get('doctrine.orm.default_entity_manager'));
        $this->assertInstanceOf('Doctrine2\DBAL\Connection', $container->get('database_connection'));
        $this->assertInstanceOf('Doctrine2\ORM\EntityManager', $container->get('doctrine.orm.entity_manager'));
        $this->assertInstanceOf('Doctrine2\Common\EventManager', $container->get('doctrine.orm.default_entity_manager.event_manager'));
        $this->assertInstanceOf('Doctrine2\Common\EventManager', $container->get('doctrine.dbal.event_manager'));
        $this->assertInstanceOf('Doctrine2\DBAL\Event\Listeners\MysqlSessionInit', $container->get('doctrine.dbal.default_connection.events.mysqlsessioninit'));
        $this->assertInstanceOf('Symfony\Bridge\Doctrine2\CacheWarmer\ProxyCacheWarmer', $container->get('doctrine.orm.proxy_cache_warmer'));
        $this->assertInstanceOf('Symfony\Bundle\DoctrineBundle\Registry', $container->get('doctrine'));
        $this->assertInstanceOf('Symfony\Bridge\Doctrine2\Validator\Constraints\UniqueEntityValidator', $container->get('doctrine.orm.validator.unique'));

        $this->assertSame($container->get('my.platform'), $container->get('doctrine.dbal.default_connection')->getDatabasePlatform());

        $this->assertTrue(Type::hasType('test'));
    }
}
