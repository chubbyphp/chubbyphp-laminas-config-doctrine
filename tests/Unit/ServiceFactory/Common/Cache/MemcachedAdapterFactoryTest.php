<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\MemcachedAdapterFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\MemcachedAdapterFactory
 *
 * @internal
 */
final class MemcachedAdapterFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var \Memcached $client */
        $client = $this->getMockByCalls(\Memcached::class);

        /** @var MarshallerInterface $marshaller */
        $marshaller = $this->getMockByCalls(MarshallerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'memcached' => [
                            'client' => 'client',
                            'namespace' => 'some_namespace',
                            'defaultLifetime' => 120,
                            'marshaller' => 'marshaller',
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with('client')->willReturn(true),
            Call::create('get')->with('client')->willReturn($client),
            Call::create('has')->with('marshaller')->willReturn(true),
            Call::create('get')->with('marshaller')->willReturn($marshaller),
        ]);

        $factory = new MemcachedAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(MemcachedAdapter::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var \Memcached $client */
        $client = $this->getMockByCalls(\Memcached::class);

        /** @var MarshallerInterface $marshaller */
        $marshaller = $this->getMockByCalls(MarshallerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'memcached' => [
                            'default' => [
                                'client' => 'client',
                                'namespace' => 'some_namespace',
                                'defaultLifetime' => 120,
                                'marshaller' => 'marshaller',
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with('client')->willReturn(true),
            Call::create('get')->with('client')->willReturn($client),
            Call::create('has')->with('marshaller')->willReturn(true),
            Call::create('get')->with('marshaller')->willReturn($marshaller),
        ]);

        $factory = [MemcachedAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MemcachedAdapter::class, $service);
    }
}
