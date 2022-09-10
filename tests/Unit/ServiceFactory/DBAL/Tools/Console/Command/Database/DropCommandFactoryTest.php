<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\Database\DropCommandFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\Database\DropCommandFactory
 *
 * @internal
 */
final class DropCommandFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $connectionProvider */
        $connectionProvider = $this->getMockByCalls(ConnectionProvider::class, []);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(ConnectionProvider::class)->willReturn(true),
            Call::create('get')->with(ConnectionProvider::class)->willReturn($connectionProvider),
        ]);

        $factory = new DropCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(DropCommand::class, $entityManagerCommand);
    }
}
