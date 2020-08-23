<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\ConnectionCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\RunSqlCommandFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\RunSqlCommandFactory
 *
 * @internal
 */
final class RunSqlCommandFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new RunSqlCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(ConnectionCommand::class, $entityManagerCommand);

        $commandReflectionProperty = new \ReflectionProperty($entityManagerCommand, 'command');
        $commandReflectionProperty->setAccessible(true);

        self::assertInstanceOf(RunSqlCommand::class, $commandReflectionProperty->getValue($entityManagerCommand));
    }
}
