<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\MemcachedCacheFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Cache\MemcachedCache;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\MemcachedCacheFactory
 *
 * @internal
 */
final class MemcachedCacheFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'memcached' => ['namespace' => 'doctrine-cache'],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = new MemcachedCacheFactory();

        $service = $factory($container);

        self::assertInstanceOf(MemcachedCache::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'memcached' => [
                            'default' => ['namespace' => 'doctrine-cache'],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = [MemcachedCacheFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MemcachedCache::class, $service);
    }
}
