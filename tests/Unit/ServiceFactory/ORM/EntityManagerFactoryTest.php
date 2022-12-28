<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\EventManager;
use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory
 *
 * @internal
 */
final class EntityManagerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var EventManager $eventManager */
        $eventManager = $this->getMockByCalls(EventManager::class);

        /** @var Connection $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getEventManager')->with()->willReturn($eventManager),
        ]);

        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $this->getMockByCalls(MappingDriver::class);

        /** @var Cache $cache */
        $cache = $this->getMockByCalls(Cache::class);

        /** @var RepositoryFactory $repositoryFactory */
        $repositoryFactory = $this->getMockByCalls(RepositoryFactory::class);

        /** @var DefaultEntityListenerResolver $entityListenerResolver */
        $entityListenerResolver = $this->getMockByCalls(DefaultEntityListenerResolver::class);

        /** @var Configuration $configuration */
        $configuration = $this->getMockByCalls(Configuration::class, [
            Call::create('getMetadataDriverImpl')->with()->willReturn($mappingDriver),
            Call::create('getClassMetadataFactoryName')->with()->willReturn(ClassMetadataFactory::class),
            Call::create('getMetadataCache')->with()->willReturn(null),
            Call::create('getMetadataCacheImpl')->with()->willReturn($cache),
            Call::create('getRepositoryFactory')->with()->willReturn($repositoryFactory),
            Call::create('getEntityListenerResolver')->with()->willReturn($entityListenerResolver),
            Call::create('isSecondLevelCacheEnabled')->with()->willReturn(false),
            Call::create('getProxyDir')->with()->willReturn('/tmp/doctrine/orm/proxies'),
            Call::create('getProxyNamespace')->with()->willReturn('DoctrineORMProxy'),
            Call::create('getAutoGenerateProxyClasses')->with()->willReturn(AbstractProxyFactory::AUTOGENERATE_ALWAYS),
            Call::create('isLazyGhostObjectEnabled')->with()->willReturn(false),
            Call::create('isSecondLevelCacheEnabled')->with()->willReturn(false),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(Connection::class)->willReturn(true),
            Call::create('get')->with(Connection::class)->willReturn($connection),
            Call::create('has')->with(Configuration::class)->willReturn(true),
            Call::create('get')->with(Configuration::class)->willReturn($configuration),
        ]);

        $factory = new EntityManagerFactory();

        $service = $factory($container);

        self::assertInstanceOf(EntityManager::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var EventManager $eventManager */
        $eventManager = $this->getMockByCalls(EventManager::class);

        /** @var Connection $connection */
        $connection = $this->getMockByCalls(Connection::class, [
            Call::create('getEventManager')->with()->willReturn($eventManager),
        ]);

        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $this->getMockByCalls(MappingDriver::class);

        /** @var Cache $cache */
        $cache = $this->getMockByCalls(Cache::class);

        /** @var RepositoryFactory $repositoryFactory */
        $repositoryFactory = $this->getMockByCalls(RepositoryFactory::class);

        /** @var DefaultEntityListenerResolver $entityListenerResolver */
        $entityListenerResolver = $this->getMockByCalls(DefaultEntityListenerResolver::class);

        /** @var Configuration $configuration */
        $configuration = $this->getMockByCalls(Configuration::class, [
            Call::create('getMetadataDriverImpl')->with()->willReturn($mappingDriver),
            Call::create('getClassMetadataFactoryName')->with()->willReturn(ClassMetadataFactory::class),
            Call::create('getMetadataCache')->with()->willReturn(null),
            Call::create('getMetadataCacheImpl')->with()->willReturn($cache),
            Call::create('getRepositoryFactory')->with()->willReturn($repositoryFactory),
            Call::create('getEntityListenerResolver')->with()->willReturn($entityListenerResolver),
            Call::create('isSecondLevelCacheEnabled')->with()->willReturn(false),
            Call::create('getProxyDir')->with()->willReturn('/tmp/doctrine/orm/proxies'),
            Call::create('getProxyNamespace')->with()->willReturn('DoctrineORMProxy'),
            Call::create('getAutoGenerateProxyClasses')->with()->willReturn(AbstractProxyFactory::AUTOGENERATE_ALWAYS),
            Call::create('isLazyGhostObjectEnabled')->with()->willReturn(false),
            Call::create('isSecondLevelCacheEnabled')->with()->willReturn(false),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(Connection::class.'default')->willReturn(true),
            Call::create('get')->with(Connection::class.'default')->willReturn($connection),
            Call::create('has')->with(Configuration::class.'default')->willReturn(true),
            Call::create('get')->with(Configuration::class.'default')->willReturn($configuration),
        ]);

        $factory = [EntityManagerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(EntityManager::class, $service);
    }
}
