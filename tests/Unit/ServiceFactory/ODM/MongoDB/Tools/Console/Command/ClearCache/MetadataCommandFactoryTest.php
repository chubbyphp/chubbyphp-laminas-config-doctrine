<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ODM\MongoDB\Tools\Console\Command\ClearCache;

use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommandFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommandFactory
 *
 * @internal
 */
final class MetadataCommandFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new MetadataCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(DocumentManagerCommand::class, $entityManagerCommand);

        $commandReflectionProperty = new \ReflectionProperty($entityManagerCommand, 'command');
        $commandReflectionProperty->setAccessible(true);

        self::assertInstanceOf(MetadataCommand::class, $commandReflectionProperty->getValue($entityManagerCommand));
    }
}
