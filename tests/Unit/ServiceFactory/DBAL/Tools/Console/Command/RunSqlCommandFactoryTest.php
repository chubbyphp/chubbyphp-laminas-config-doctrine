<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\RunSqlCommandFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\RunSqlCommandFactory
 *
 * @internal
 */
final class RunSqlCommandFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $builder->create(ConnectionProvider::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [ConnectionProvider::class], true),
            new WithReturn('get', [ConnectionProvider::class], $connectionProvider),
        ]);

        $factory = new RunSqlCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(RunSqlCommand::class, $entityManagerCommand);
    }
}
