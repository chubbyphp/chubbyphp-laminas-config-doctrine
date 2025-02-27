<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ORM\Tools\Console;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\ContainerEntityManagerProvider;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\ContainerEntityManagerProvider
 *
 * @internal
 */
final class ContainerEntityManagerProviderTest extends TestCase
{
    public function testGetDefaultManager(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $builder->create(EntityManagerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [EntityManagerInterface::class], $entityManager),
        ]);

        $connectionProvider = new ContainerEntityManagerProvider($container);

        self::assertSame($connectionProvider->getDefaultManager(), $entityManager);
    }

    public function testGetManager(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $builder->create(EntityManagerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [EntityManagerInterface::class.'name'], $entityManager),
        ]);

        $connectionProvider = new ContainerEntityManagerProvider($container);

        self::assertSame($connectionProvider->getManager('name'), $entityManager);
    }
}
