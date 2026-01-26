<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayAdapterFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayAdapterFactory
 *
 * @internal
 */
final class ArrayAdapterFactoryTest extends TestCase
{
    public function testInvokeWithDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], []),
        ]);

        $factory = new ArrayAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ArrayAdapter::class, $service);

        self::assertSame(0, self::getPrivateProperty($service, 'defaultLifetime'));
        self::assertTrue(self::getPrivateProperty($service, 'storeSerialized'));
        self::assertSame(0.0, self::getPrivateProperty($service, 'maxLifetime'));
        self::assertSame(0, self::getPrivateProperty($service, 'maxItems'));
    }

    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'cache' => [
                        'array' => [
                            'defaultLifetime' => 120,
                            'storeSerialized' => false,
                            'maxLifetime' => 600,
                            'maxItems' => 20,
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = new ArrayAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ArrayAdapter::class, $service);

        self::assertSame(120, self::getPrivateProperty($service, 'defaultLifetime'));
        self::assertFalse(self::getPrivateProperty($service, 'storeSerialized'));
        self::assertSame(600.0, self::getPrivateProperty($service, 'maxLifetime'));
        self::assertSame(20, self::getPrivateProperty($service, 'maxItems'));
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'cache' => [
                        'array' => [
                            'default' => [
                                'defaultLifetime' => 120,
                                'storeSerialized' => false,
                                'maxLifetime' => 600,
                                'maxItems' => 20,
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = [ArrayAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ArrayAdapter::class, $service);

        self::assertSame(120, self::getPrivateProperty($service, 'defaultLifetime'));
        self::assertFalse(self::getPrivateProperty($service, 'storeSerialized'));
        self::assertSame(600.0, self::getPrivateProperty($service, 'maxLifetime'));
        self::assertSame(20, self::getPrivateProperty($service, 'maxItems'));
    }

    private static function getPrivateProperty(object $object, string $property): mixed
    {
        $reflection = new \ReflectionProperty($object, $property);

        return $reflection->getValue($object);
    }
}
