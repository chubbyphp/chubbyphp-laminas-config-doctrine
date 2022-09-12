<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayAdapterFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([]),
        ]);

        $factory = new ArrayAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ArrayAdapter::class, $service);
    }

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
    }
}
