<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapDriver;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory
 *
 * @internal
 */
final class ClassMapDriverFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'driver' => [
                        'classMap' => ['map' => ['class' => 'mappingClass']],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['mappingClass'], false),
        ]);

        $factory = new ClassMapDriverFactory();

        $service = $factory($container);

        self::assertInstanceOf(ClassMapDriver::class, $service);

        $mapReflectionProperty = new \ReflectionProperty($service, 'map');
        $mapReflectionProperty->setAccessible(true);

        self::assertSame(['class' => 'mappingClass'], $mapReflectionProperty->getValue($service));
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'driver' => [
                        'classMap' => [
                            'default' => ['map' => ['class' => 'mappingClass']],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['mappingClass'], false),
        ]);

        $factory = [ClassMapDriverFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ClassMapDriver::class, $service);

        $mapReflectionProperty = new \ReflectionProperty($service, 'map');
        $mapReflectionProperty->setAccessible(true);

        self::assertSame(['class' => 'mappingClass'], $mapReflectionProperty->getValue($service));
    }
}
