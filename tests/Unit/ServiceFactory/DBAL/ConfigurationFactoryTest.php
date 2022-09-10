<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConfigurationFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Configuration;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConfigurationFactory
 *
 * @internal
 */
final class ConfigurationFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var CacheItemPoolInterface $cache */
        $cache = $this->getMockByCalls(CacheItemPoolInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'dbal' => [
                        'configuration' => [
                            'resultCache' => CacheItemPoolInterface::class,
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(CacheItemPoolInterface::class)->willReturn(true),
            Call::create('get')->with(CacheItemPoolInterface::class)->willReturn($cache),
        ]);

        $factory = new ConfigurationFactory();

        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var CacheItemPoolInterface $cache */
        $cache = $this->getMockByCalls(CacheItemPoolInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'dbal' => [
                        'configuration' => [
                            'default' => [
                                'resultCache' => CacheItemPoolInterface::class,
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(CacheItemPoolInterface::class)->willReturn(true),
            Call::create('get')->with(CacheItemPoolInterface::class)->willReturn($cache),
        ]);

        $factory = [ConfigurationFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);
    }
}
