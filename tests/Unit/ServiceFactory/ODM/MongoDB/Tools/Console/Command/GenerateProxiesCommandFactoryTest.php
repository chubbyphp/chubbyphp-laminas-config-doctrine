<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ODM\MongoDB\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommandFactory;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommandFactory
 *
 * @internal
 */
final class GenerateProxiesCommandFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $factory = new GenerateProxiesCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(DocumentManagerCommand::class, $entityManagerCommand);

        $commandReflectionProperty = new \ReflectionProperty($entityManagerCommand, 'command');
        $commandReflectionProperty->setAccessible(true);

        self::assertInstanceOf(
            GenerateProxiesCommand::class,
            $commandReflectionProperty->getValue($entityManagerCommand)
        );
    }
}
