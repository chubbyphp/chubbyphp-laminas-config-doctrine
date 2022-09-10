<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\Database\CreateCommandFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\Database\CreateCommandFactory
 *
 * @internal
 */
final class CreateCommandFactoryTest extends TestCase
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

        $factory = new CreateCommandFactory();

        $entityManagerCommand = $factory($container);

        self::assertInstanceOf(CreateCommand::class, $entityManagerCommand);
    }
}
