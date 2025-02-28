<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\ContainerConnectionProvider;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\ContainerConnectionProvider
 *
 * @internal
 */
final class ContainerConnectionProviderTest extends TestCase
{
    public function testGetDefaultConnection(): void
    {
        $builder = new MockObjectBuilder();

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [Connection::class], $connection),
        ]);

        $connectionProvider = new ContainerConnectionProvider($container);

        self::assertSame($connection, $connectionProvider->getDefaultConnection());
    }

    public function testGetConnection(): void
    {
        $builder = new MockObjectBuilder();

        /** @var Connection $connection */
        $connection = $builder->create(Connection::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [Connection::class.'name'], $connection),
        ]);

        $connectionProvider = new ContainerConnectionProvider($container);

        self::assertSame($connection, $connectionProvider->getConnection('name'));
    }
}
