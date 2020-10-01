<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\MongoDB;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\MongoDB\ClientFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use MongoDB\Client;
use MongoDB\Model\BSONArray;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\MongoDB\ClientFactory
 *
 * @internal
 */
final class ClientFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'mongodb' => [
                        'client' => [],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('mongodb://127.0.0.1/')->willReturn(false),
        ]);

        $factory = new ClientFactory();

        $service = $factory($container);

        self::assertInstanceOf(Client::class, $service);
    }

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'mongodb' => [
                        'client' => [
                            'uri' => 'mongodb://host:9876/',
                            'uriOptions' => [
                                'appname' => 'app',
                            ],
                            'driverOptions' => [
                                'typeMap' => [
                                    'array' => BSONArray::class,
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('mongodb://host:9876/')->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('app')->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with(BSONArray::class)->willReturn(false),
        ]);

        $factory = new ClientFactory();

        $service = $factory($container);

        self::assertInstanceOf(Client::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'mongodb' => [
                        'client' => [
                            'default' => [
                                'uri' => 'mongodb://host:9876/',
                                'uriOptions' => [
                                    'appname' => 'app',
                                ],
                                'driverOptions' => [
                                    'typeMap' => [
                                        'array' => BSONArray::class,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('mongodb://host:9876/')->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('app')->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with(BSONArray::class)->willReturn(false),
        ]);

        $factory = [ClientFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(Client::class, $service);
    }
}
