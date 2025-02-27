<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConnectionFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\SchemaManagerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConnectionFactory
 *
 * @internal
 */
final class ConnectionFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var SchemaManagerFactory $schemaManagerFactory */
        $schemaManagerFactory = $builder->create(SchemaManagerFactory::class, []);

        /** @var Configuration $configuration */
        $configuration = $builder->create(Configuration::class, [
            new WithReturn('getMiddlewares', [], []),
            new WithReturn('getAutoCommit', [], false),
            new WithReturn('getSchemaManagerFactory', [], $schemaManagerFactory),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [Configuration::class], true),
            new WithReturn('get', [Configuration::class], $configuration),
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'dbal' => [
                        'connection' => [
                            'driver' => 'pdo_sqlite',
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = new ConnectionFactory();

        $service = $factory($container);

        self::assertInstanceOf(Connection::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var SchemaManagerFactory $schemaManagerFactory */
        $schemaManagerFactory = $builder->create(SchemaManagerFactory::class, []);

        /** @var Configuration $configuration */
        $configuration = $builder->create(Configuration::class, [
            new WithReturn('getMiddlewares', [], []),
            new WithReturn('getAutoCommit', [], false),
            new WithReturn('getSchemaManagerFactory', [], $schemaManagerFactory),
        ]);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [Configuration::class.'default'], true),
            new WithReturn('get', [Configuration::class.'default'], $configuration),
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'dbal' => [
                        'connection' => [
                            'default' => [
                                'driver' => 'pdo_sqlite',
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = [ConnectionFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(Connection::class, $service);
    }
}
