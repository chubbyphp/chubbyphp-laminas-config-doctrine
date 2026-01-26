<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ApcuAdapterFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ApcuAdapterFactory
 *
 * @internal
 */
final class ApcuAdapterFactoryTest extends TestCase
{
    public function testInvokeWithDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], []),
        ]);

        $factory = new ApcuAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ApcuAdapter::class, $service);

        self::assertSame('', self::getPrivateProperty($service, 'namespace'));
        self::assertSame(0, self::getPrivateProperty($service, 'defaultLifetime'));
    }

    public function testInvokeWithEmptyConfig(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'cache' => [
                        'apcu' => [],
                    ],
                ],
            ]),
        ]);

        $factory = new ApcuAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ApcuAdapter::class, $service);

        self::assertSame('', self::getPrivateProperty($service, 'namespace'));
        self::assertSame(0, self::getPrivateProperty($service, 'defaultLifetime'));
    }

    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MarshallerInterface $marshaller */
        $marshaller = $builder->create(MarshallerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'cache' => [
                        'apcu' => [
                            'namespace' => 'some_namespace',
                            'defaultLifetime' => 120,
                            'version' => '1.2',
                            'marshaller' => 'marshaller',
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', ['marshaller'], true),
            new WithReturn('get', ['marshaller'], $marshaller),
        ]);

        $factory = new ApcuAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ApcuAdapter::class, $service);

        self::assertSame('some_namespace:', self::getPrivateProperty($service, 'namespace'));
        self::assertSame(120, self::getPrivateProperty($service, 'defaultLifetime'));
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MarshallerInterface $marshaller */
        $marshaller = $builder->create(MarshallerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'cache' => [
                        'apcu' => [
                            'default' => [
                                'namespace' => 'some_namespace',
                                'defaultLifetime' => 120,
                                'version' => '1.2',
                                'marshaller' => 'marshaller',
                            ],
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', ['marshaller'], true),
            new WithReturn('get', ['marshaller'], $marshaller),
        ]);

        $factory = [ApcuAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ApcuAdapter::class, $service);

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
