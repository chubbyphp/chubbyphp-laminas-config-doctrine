<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ChainCacheFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ChainCache;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ChainCacheFactory
 *
 * @internal
 */
final class ChainCacheFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ArrayCache $arrayCache */
        $arrayCache = $this->getMockByCalls(ArrayCache::class, [
            Call::create('setNamespace')->with('doctrine-cache'),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'chain' => [
                            'cacheProviders' => [
                                ArrayCache::class,
                            ],
                            'namespace' => 'doctrine-cache',
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(ArrayCache::class)->willReturn(true),
            Call::create('get')->with(ArrayCache::class)->willReturn($arrayCache),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = new ChainCacheFactory();

        $service = $factory($container);

        self::assertInstanceOf(ChainCache::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ArrayCache $arrayCache */
        $arrayCache = $this->getMockByCalls(ArrayCache::class, [
            Call::create('setNamespace')->with('doctrine-cache'),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'chain' => [
                            'default' => [
                                'cacheProviders' => [
                                    ArrayCache::class,
                                ],
                                'namespace' => 'doctrine-cache',
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(ArrayCache::class)->willReturn(true),
            Call::create('get')->with(ArrayCache::class)->willReturn($arrayCache),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = [ChainCacheFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ChainCache::class, $service);
    }
}
