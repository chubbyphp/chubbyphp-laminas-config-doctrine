<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\RedisAdapterFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvokeWithDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var \Redis $redis */
        $redis = $builder->create(\Redis::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'cache' => [
                        'redis' => [
                            'redis' => 'redis',
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', ['redis'], true),
            new WithReturn('get', ['redis'], $redis),
        ]);

        $factory = new RedisAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(RedisAdapter::class, $service);

        self::assertSame('', self::getPrivateProperty($service, 'namespace'));
        self::assertSame(0, self::getPrivateProperty($service, 'defaultLifetime'));
    }

    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var \Redis $redis */
        $redis = $builder->create(\Redis::class, []);

        /** @var MarshallerInterface $marshaller */
        $marshaller = $builder->create(MarshallerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
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
            new WithReturn('has', ['redis'], true),
            new WithReturn('get', ['redis'], $redis),
            new WithReturn('has', ['marshaller'], true),
            new WithReturn('get', ['marshaller'], $marshaller),
        ]);

        $factory = new RedisAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(RedisAdapter::class, $service);

        self::assertSame('some_namespace:', self::getPrivateProperty($service, 'namespace'));
        self::assertSame(120, self::getPrivateProperty($service, 'defaultLifetime'));
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var \Redis $redis */
        $redis = $builder->create(\Redis::class, []);

        /** @var MarshallerInterface $marshaller */
        $marshaller = $builder->create(MarshallerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
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
            new WithReturn('has', ['redis'], true),
            new WithReturn('get', ['redis'], $redis),
            new WithReturn('has', ['marshaller'], true),
            new WithReturn('get', ['marshaller'], $marshaller),
        ]);

        $factory = [RedisAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(RedisAdapter::class, $service);

        self::assertSame('some_namespace:', self::getPrivateProperty($service, 'namespace'));
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
