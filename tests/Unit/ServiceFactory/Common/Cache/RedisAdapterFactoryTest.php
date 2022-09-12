<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\RedisAdapterFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\RedisAdapterFactory
 *
 * @internal
 */
final class RedisAdapterFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var \Redis $redis */
        $redis = $this->getMockByCalls(\Redis::class);

        /** @var MarshallerInterface $marshaller */
        $marshaller = $this->getMockByCalls(MarshallerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'redis' => [
                            'redis' => 'redis',
                            'namespace' => 'some_namespace',
                            'defaultLifetime' => 120,
                            'marshaller' => 'marshaller',
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with('redis')->willReturn(true),
            Call::create('get')->with('redis')->willReturn($redis),
            Call::create('has')->with('marshaller')->willReturn(true),
            Call::create('get')->with('marshaller')->willReturn($marshaller),
        ]);

        $factory = new RedisAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(RedisAdapter::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var \Redis $redis */
        $redis = $this->getMockByCalls(\Redis::class);

        /** @var MarshallerInterface $marshaller */
        $marshaller = $this->getMockByCalls(MarshallerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'redis' => [
                            'default' => [
                                'redis' => 'redis',
                                'namespace' => 'some_namespace',
                                'defaultLifetime' => 120,
                                'marshaller' => 'marshaller',
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with('redis')->willReturn(true),
            Call::create('get')->with('redis')->willReturn($redis),
            Call::create('has')->with('marshaller')->willReturn(true),
            Call::create('get')->with('marshaller')->willReturn($marshaller),
        ]);

        $factory = [RedisAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(RedisAdapter::class, $service);
    }
}
