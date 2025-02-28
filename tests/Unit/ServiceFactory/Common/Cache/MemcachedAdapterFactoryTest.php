<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\MemcachedAdapterFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var \Memcached $client */
        $client = $builder->create(\Memcached::class, []);

        /** @var MarshallerInterface $marshaller */
        $marshaller = $builder->create(MarshallerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
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
            new WithReturn('has', ['client'], true),
            new WithReturn('get', ['client'], $client),
            new WithReturn('has', ['marshaller'], true),
            new WithReturn('get', ['marshaller'], $marshaller),
        ]);

        $factory = new MemcachedAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(MemcachedAdapter::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var \Memcached $client */
        $client = $builder->create(\Memcached::class, []);

        /** @var MarshallerInterface $marshaller */
        $marshaller = $builder->create(MarshallerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
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
            new WithReturn('has', ['client'], true),
            new WithReturn('get', ['client'], $client),
            new WithReturn('has', ['marshaller'], true),
            new WithReturn('get', ['marshaller'], $marshaller),
        ]);

        $factory = [MemcachedAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MemcachedAdapter::class, $service);
    }
}
