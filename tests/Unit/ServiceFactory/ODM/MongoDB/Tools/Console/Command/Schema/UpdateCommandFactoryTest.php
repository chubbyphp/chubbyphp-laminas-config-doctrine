<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ODM\MongoDB\Tools\Console\Command\Schema;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command\Schema\UpdateCommandFactory;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\UpdateCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command\Schema\UpdateCommandFactory
 *
 * @internal
 */
final class UpdateCommandFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, []);

        $factory = new UpdateCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(DocumentManagerCommand::class, $entityManagerCommand);

        $commandReflectionProperty = new \ReflectionProperty($entityManagerCommand, 'command');

        self::assertInstanceOf(UpdateCommand::class, $commandReflectionProperty->getValue($entityManagerCommand));
    }
}
