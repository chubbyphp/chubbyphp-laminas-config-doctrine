<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConnectionFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\SchemaManagerFactory;
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
        /** @var SchemaManagerFactory $schemaManagerFactory */
        $schemaManagerFactory = $this->getMockByCalls(SchemaManagerFactory::class, []);

        /** @var Configuration $configuration */
        $configuration = $this->getMockByCalls(Configuration::class, [
            Call::create('getMiddlewares')->with()->willReturn([]),
            Call::create('getAutoCommit')->with()->willReturn(false),
            Call::create('getSchemaManagerFactory')->with()->willReturn($schemaManagerFactory),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(Configuration::class)->willReturn(true),
            Call::create('get')->with(Configuration::class)->willReturn($configuration),
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
        /** @var SchemaManagerFactory $schemaManagerFactory */
        $schemaManagerFactory = $this->getMockByCalls(SchemaManagerFactory::class, []);

        /** @var Configuration $configuration */
        $configuration = $this->getMockByCalls(Configuration::class, [
            Call::create('getMiddlewares')->with()->willReturn([]),
            Call::create('getAutoCommit')->with()->willReturn(false),
            Call::create('getSchemaManagerFactory')->with()->willReturn($schemaManagerFactory),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(Configuration::class.'default')->willReturn(true),
            Call::create('get')->with(Configuration::class.'default')->willReturn($configuration),
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
