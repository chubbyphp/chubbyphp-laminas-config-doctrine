<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConnectionFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConnectionFactory
 *
 * @internal
 */
final class ConnectionFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->getMockByCalls(Configuration::class, [
            Call::create('getAutoCommit')->with()->willReturn(false),
        ]);

        /** @var EventManager $eventManager */
        $eventManager = $this->getMockByCalls(EventManager::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(Configuration::class)->willReturn(true),
            Call::create('get')->with(Configuration::class)->willReturn($configuration),
            Call::create('has')->with(EventManager::class)->willReturn(true),
            Call::create('get')->with(EventManager::class)->willReturn($eventManager),
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'dbal' => [
                        'connection' => [
                            'driver' => 'pdo_sqlite',
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = new ConnectionFactory();

        $service = $factory($container);

        self::assertInstanceOf(Connection::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->getMockByCalls(Configuration::class, [
            Call::create('getAutoCommit')->with()->willReturn(false),
        ]);

        /** @var EventManager $eventManager */
        $eventManager = $this->getMockByCalls(EventManager::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(Configuration::class.'default')->willReturn(true),
            Call::create('get')->with(Configuration::class.'default')->willReturn($configuration),
            Call::create('has')->with(EventManager::class.'default')->willReturn(true),
            Call::create('get')->with(EventManager::class.'default')->willReturn($eventManager),
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'dbal' => [
                        'connection' => [
                            'default' => [
                                'driver' => 'pdo_sqlite',
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = [ConnectionFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(Connection::class, $service);
    }
}
