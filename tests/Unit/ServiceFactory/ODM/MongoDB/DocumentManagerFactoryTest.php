<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ODM\MongoDB;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\DocumentManagerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Repository\RepositoryFactory;
use MongoDB\Client;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\DocumentManagerFactory
 *
 * @internal
 */
final class DocumentManagerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var Client $client */
        $client = $builder->create(Client::class, []);

        /** @var CacheItemPoolInterface $cache */
        $cache = $builder->create(CacheItemPoolInterface::class, []);

        /** @var RepositoryFactory $repositoryFactory */
        $repositoryFactory = $builder->create(RepositoryFactory::class, []);

        /** @var Configuration $configuration */
        $configuration = $builder->create(Configuration::class, [
            new WithReturn('isLazyGhostObjectEnabled', [], true),
            new WithReturn('getClassMetadataFactoryName', [], ClassMetadataFactory::class),
            new WithReturn('getMetadataCache', [], $cache),
            new WithReturn('getHydratorDir', [], '/tmp/doctrine/orm/hydrators'),
            new WithReturn('getHydratorNamespace', [], 'DoctrineMongoDBODMHydrator'),
            new WithReturn('getAutoGenerateHydratorClasses', [], Configuration::AUTOGENERATE_ALWAYS),
            new WithReturn('isNativeLazyObjectEnabled', [], false),
            new WithReturn('isLazyGhostObjectEnabled', [], true),
            new WithReturn('getProxyDir', [], '/tmp/doctrine/orm/proxies'),
            new WithReturn('getProxyNamespace', [], 'DoctrineMongoDBODMProxy'),
            new WithReturn('getAutoGenerateProxyClasses', [], Configuration::AUTOGENERATE_ALWAYS),
            new WithReturn('getRepositoryFactory', [], $repositoryFactory),
        ]);

        /** @var EventManager $eventManager */
        $eventManager = $builder->create(EventManager::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [Client::class], true),
            new WithReturn('get', [Client::class], $client),
            new WithReturn('has', [Configuration::class], true),
            new WithReturn('get', [Configuration::class], $configuration),
            new WithReturn('has', [EventManager::class], true),
            new WithReturn('get', [EventManager::class], $eventManager),
        ]);

        $factory = new DocumentManagerFactory();

        $service = $factory($container);

        self::assertInstanceOf(DocumentManager::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var Client $client */
        $client = $builder->create(Client::class, []);

        /** @var CacheItemPoolInterface $cache */
        $cache = $builder->create(CacheItemPoolInterface::class, []);

        /** @var RepositoryFactory $repositoryFactory */
        $repositoryFactory = $builder->create(RepositoryFactory::class, []);

        /** @var Configuration $configuration */
        $configuration = $builder->create(Configuration::class, [
            new WithReturn('isLazyGhostObjectEnabled', [], true),
            new WithReturn('getClassMetadataFactoryName', [], ClassMetadataFactory::class),
            new WithReturn('getMetadataCache', [], $cache),
            new WithReturn('getHydratorDir', [], '/tmp/doctrine/orm/hydrators'),
            new WithReturn('getHydratorNamespace', [], 'DoctrineMongoDBODMHydrator'),
            new WithReturn('getAutoGenerateHydratorClasses', [], Configuration::AUTOGENERATE_ALWAYS),
            new WithReturn('isNativeLazyObjectEnabled', [], false),
            new WithReturn('isLazyGhostObjectEnabled', [], true),
            new WithReturn('getProxyDir', [], '/tmp/doctrine/orm/proxies'),
            new WithReturn('getProxyNamespace', [], 'DoctrineMongoDBODMProxy'),
            new WithReturn('getAutoGenerateProxyClasses', [], Configuration::AUTOGENERATE_ALWAYS),
            new WithReturn('getRepositoryFactory', [], $repositoryFactory),
        ]);

        /** @var EventManager $eventManager */
        $eventManager = $builder->create(EventManager::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [Client::class.'default'], true),
            new WithReturn('get', [Client::class.'default'], $client),
            new WithReturn('has', [Configuration::class.'default'], true),
            new WithReturn('get', [Configuration::class.'default'], $configuration),
            new WithReturn('has', [EventManager::class.'default'], true),
            new WithReturn('get', [EventManager::class.'default'], $eventManager),
        ]);

        $factory = [DocumentManagerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(DocumentManager::class, $service);
    }
}
