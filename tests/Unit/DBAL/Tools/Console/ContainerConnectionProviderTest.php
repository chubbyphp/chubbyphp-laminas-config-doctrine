<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\DBAL\Tools\Console;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\ContainerConnectionProvider;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\ContainerConnectionProvider
 *
 * @internal
 */
final class ContainerConnectionProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetDefaultConnection(): void
    {
        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(Connection::class)->willReturn($connection),
        ]);

        $connectionProvider = new ContainerConnectionProvider($container);

        self::assertSame($connectionProvider->getDefaultConnection(), $connection);
    }

    public function testGetConnection(): void
    {
        /** @var Connection|MockObject $connection */
        $connection = $this->getMockByCalls(Connection::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(Connection::class.'name')->willReturn($connection),
        ]);

        $connectionProvider = new ContainerConnectionProvider($container);

        self::assertSame($connectionProvider->getConnection('name'), $connection);
    }
}
