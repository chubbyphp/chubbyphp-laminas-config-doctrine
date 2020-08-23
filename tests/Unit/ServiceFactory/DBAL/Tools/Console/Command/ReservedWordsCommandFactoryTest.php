<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\ConnectionCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\ReservedWordsCommandFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\ReservedWordsCommandFactory
 *
 * @internal
 */
final class ReservedWordsCommandFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new ReservedWordsCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(ConnectionCommand::class, $entityManagerCommand);

        $commandReflectionProperty = new \ReflectionProperty($entityManagerCommand, 'command');
        $commandReflectionProperty->setAccessible(true);

        self::assertInstanceOf(ReservedWordsCommand::class, $commandReflectionProperty->getValue($entityManagerCommand));
    }
}
