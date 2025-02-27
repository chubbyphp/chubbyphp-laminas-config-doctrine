<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ValidateSchemaCommandFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ValidateSchemaCommandFactory
 *
 * @internal
 */
final class ValidateSchemaCommandFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $builder->create(EntityManagerProvider::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [EntityManagerProvider::class], true),
            new WithReturn('get', [EntityManagerProvider::class], $entityManagerProvider),
        ]);

        $factory = new ValidateSchemaCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(ValidateSchemaCommand::class, $entityManagerCommand);
    }
}
