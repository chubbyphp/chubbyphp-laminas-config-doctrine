<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ChainAdapterFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvokeWithDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var CacheItemPoolInterface $cache */
        $cache = $builder->create(CacheItemPoolInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'cache' => [
                        'chain' => [
                            'adapters' => [ArrayAdapter::class],
                        ],
                    ],
                ],
            ]),
            // Simulate service resolution: the container returns $cache for ArrayAdapter::class.
            new WithReturn('has', [ArrayAdapter::class], true),
            new WithReturn('get', [ArrayAdapter::class], $cache),
        ]);

        $factory = new ChainAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ChainAdapter::class, $service);

        self::assertSame(0, self::getPrivateProperty($service, 'defaultLifetime'));
    }

    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var CacheItemPoolInterface $cache */
        $cache = $builder->create(CacheItemPoolInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'cache' => [
                        'chain' => [
                            'adapters' => [ArrayAdapter::class],
                            'defaultLifetime' => 120,
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', [ArrayAdapter::class], true),
            new WithReturn('get', [ArrayAdapter::class], $cache),
        ]);

        $factory = new ChainAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ChainAdapter::class, $service);

        self::assertSame(120, self::getPrivateProperty($service, 'defaultLifetime'));
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
            new WithReturn('has', [ArrayAdapter::class], true),
            new WithReturn('get', [ArrayAdapter::class], $cache),
        ]);

        $factory = [ChainAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ChainAdapter::class, $service);

        self::assertSame(120, self::getPrivateProperty($service, 'defaultLifetime'));
    }

    private static function getPrivateProperty(object $object, string $property): mixed
    {
        $class = new \ReflectionClass($object);
        while ($class) {
            if ($class->hasProperty($property)) {
                $prop = $class->getProperty($property);

                return $prop->getValue($object);
            }
            $class = $class->getParentClass();
        }

        throw new \ReflectionException(\sprintf('Property %s does not exist', $property));
    }
}
