<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapDriver;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory
 *
 * @internal
 */
final class ClassMapDriverFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'driver' => [
                        'classMap' => ['map' => ['class' => 'mappingClass']],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('mappingClass')->willReturn(false),
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
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'driver' => [
                        'classMap' => [
                            'default' => ['map' => ['class' => 'mappingClass']],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('mappingClass')->willReturn(false),
        ]);

        $factory = [ClassMapDriverFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ClassMapDriver::class, $service);

        $mapReflectionProperty = new \ReflectionProperty($service, 'map');
        $mapReflectionProperty->setAccessible(true);

        self::assertSame(['class' => 'mappingClass'], $mapReflectionProperty->getValue($service));
    }
}
