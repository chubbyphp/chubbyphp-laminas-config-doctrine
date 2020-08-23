<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConfigurationFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Configuration;
use PHPUnit\Framework\TestCase;
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
        /** @var Cache $cache */
        $cache = $this->getMockByCalls(Cache::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'dbal' => [
                        'configuration' => [
                            'resultCacheImpl' => Cache::class,
                            'filterSchemaAssetsExpression' => 'expression',
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(Cache::class)->willReturn(true),
            Call::create('get')->with(Cache::class)->willReturn($cache),
            Call::create('has')->with('expression')->willReturn(false),
        ]);

        $factory = new ConfigurationFactory();

        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var Cache $cache */
        $cache = $this->getMockByCalls(Cache::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'dbal' => [
                        'configuration' => [
                            'default' => [
                                'resultCacheImpl' => Cache::class,
                                'filterSchemaAssetsExpression' => 'expression',
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(Cache::class)->willReturn(true),
            Call::create('get')->with(Cache::class)->willReturn($cache),
            Call::create('has')->with('expression')->willReturn(false),
        ]);

        $factory = [ConfigurationFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);
    }
}
