<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConfigurationFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var CacheItemPoolInterface $cache */
        $cache = $builder->create(CacheItemPoolInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'dbal' => [
                        'configuration' => [
                            'resultCache' => CacheItemPoolInterface::class,
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', [CacheItemPoolInterface::class], true),
            new WithReturn('get', [CacheItemPoolInterface::class], $cache),
        ]);

        $factory = new ConfigurationFactory();

        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var CacheItemPoolInterface $cache */
        $cache = $builder->create(CacheItemPoolInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
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
            new WithReturn('has', [CacheItemPoolInterface::class], true),
            new WithReturn('get', [CacheItemPoolInterface::class], $cache),
        ]);

        $factory = [ConfigurationFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);
    }
}
