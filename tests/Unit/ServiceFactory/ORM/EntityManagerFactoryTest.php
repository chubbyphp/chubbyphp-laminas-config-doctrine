<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EventManager $eventManager */
        $eventManager = $builder->create(EventManager::class, []);

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, []);

        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $builder->create(MappingDriver::class, []);

        /** @var RepositoryFactory $repositoryFactory */
        $repositoryFactory = $builder->create(RepositoryFactory::class, []);

        /** @var DefaultEntityListenerResolver $entityListenerResolver */
        $entityListenerResolver = $builder->create(DefaultEntityListenerResolver::class, []);

        /** @var Configuration $configuration */
        $configuration = $builder->create(Configuration::class, [
            new WithReturn('getMetadataDriverImpl', [], $mappingDriver),
            new WithReturn('getClassMetadataFactoryName', [], ClassMetadataFactory::class),
            new WithReturn('isNativeLazyObjectsEnabled', [], false),
            new WithReturn('getMetadataCache', [], null),
            new WithReturn('getRepositoryFactory', [], $repositoryFactory),
            new WithReturn('getEntityListenerResolver', [], $entityListenerResolver),
            new WithReturn('isSecondLevelCacheEnabled', [], false),
            new WithReturn('isNativeLazyObjectsEnabled', [], false),
            new WithReturn('getProxyDir', [], '/tmp/doctrine/orm/proxies'),
            new WithReturn('getProxyNamespace', [], 'DoctrineORMProxy'),
            new WithReturn('getAutoGenerateProxyClasses', [], AbstractProxyFactory::AUTOGENERATE_ALWAYS),
            new WithReturn('isNativeLazyObjectsEnabled', [], false),
            new WithReturn('isSecondLevelCacheEnabled', [], false),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [Connection::class], true),
            new WithReturn('get', [Connection::class], $connection),
            new WithReturn('has', [Configuration::class], true),
            new WithReturn('get', [Configuration::class], $configuration),
            new WithReturn('has', [EventManager::class], true),
            new WithReturn('get', [EventManager::class], $eventManager),
        ]);

        $factory = new EntityManagerFactory();

        $service = $factory($container);

        self::assertInstanceOf(EntityManager::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EventManager $eventManager */
        $eventManager = $builder->create(EventManager::class, []);

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, []);

        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $builder->create(MappingDriver::class, []);

        /** @var RepositoryFactory $repositoryFactory */
        $repositoryFactory = $builder->create(RepositoryFactory::class, []);

        /** @var DefaultEntityListenerResolver $entityListenerResolver */
        $entityListenerResolver = $builder->create(DefaultEntityListenerResolver::class, []);

        /** @var Configuration $configuration */
        $configuration = $builder->create(Configuration::class, [
            new WithReturn('getMetadataDriverImpl', [], $mappingDriver),
            new WithReturn('getClassMetadataFactoryName', [], ClassMetadataFactory::class),
            new WithReturn('isNativeLazyObjectsEnabled', [], false),
            new WithReturn('getMetadataCache', [], null),
            new WithReturn('getRepositoryFactory', [], $repositoryFactory),
            new WithReturn('getEntityListenerResolver', [], $entityListenerResolver),
            new WithReturn('isSecondLevelCacheEnabled', [], false),
            new WithReturn('isNativeLazyObjectsEnabled', [], false),
            new WithReturn('getProxyDir', [], '/tmp/doctrine/orm/proxies'),
            new WithReturn('getProxyNamespace', [], 'DoctrineORMProxy'),
            new WithReturn('getAutoGenerateProxyClasses', [], AbstractProxyFactory::AUTOGENERATE_ALWAYS),
            new WithReturn('isNativeLazyObjectsEnabled', [], false),
            new WithReturn('isSecondLevelCacheEnabled', [], false),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [Connection::class.'default'], true),
            new WithReturn('get', [Connection::class.'default'], $connection),
            new WithReturn('has', [Configuration::class.'default'], true),
            new WithReturn('get', [Configuration::class.'default'], $configuration),
            new WithReturn('has', [EventManager::class.'default'], true),
            new WithReturn('get', [EventManager::class.'default'], $eventManager),
        ]);

        $factory = [EntityManagerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(EntityManager::class, $service);
    }
}
