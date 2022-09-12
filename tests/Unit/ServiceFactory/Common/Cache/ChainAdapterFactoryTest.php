<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ChainAdapterFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ChainAdapterFactory
 *
 * @internal
 */
final class ChainAdapterFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var CacheItemPoolInterface $cache */
        $cache = $this->getMockByCalls(CacheItemPoolInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'chain' => [
                            'adapters' => [ArrayAdapter::class],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(ArrayAdapter::class)->willReturn(true),
            Call::create('get')->with(ArrayAdapter::class)->willReturn($cache),
        ]);

        $factory = new ChainAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ChainAdapter::class, $service);
    }

    public function testInvoke(): void
    {
        /** @var CacheItemPoolInterface $cache */
        $cache = $this->getMockByCalls(CacheItemPoolInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'chain' => [
                            'adapters' => [ArrayAdapter::class],
                            'defaultLifetime' => 120,
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(ArrayAdapter::class)->willReturn(true),
            Call::create('get')->with(ArrayAdapter::class)->willReturn($cache),
        ]);

        $factory = new ChainAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ChainAdapter::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var CacheItemPoolInterface $cache */
        $cache = $this->getMockByCalls(CacheItemPoolInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'chain' => [
                            'default' => [
                                'adapters' => [ArrayAdapter::class],
                                'defaultLifetime' => 120,
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(ArrayAdapter::class)->willReturn(true),
            Call::create('get')->with(ArrayAdapter::class)->willReturn($cache),
        ]);

        $factory = [ChainAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ChainAdapter::class, $service);
    }
}
