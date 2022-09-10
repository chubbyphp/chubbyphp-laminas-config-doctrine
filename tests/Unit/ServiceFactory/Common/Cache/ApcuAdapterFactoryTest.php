<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ApcuAdapterFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([]),
        ]);

        $factory = new ApcuAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ApcuAdapter::class, $service);
    }

    public function testInvoke(): void
    {
        /** @var MarshallerInterface $marshaller */
        $marshaller = $this->getMockByCalls(MarshallerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
            Call::create('has')->with('marshaller')->willReturn(true),
            Call::create('get')->with('marshaller')->willReturn($marshaller),
        ]);

        $factory = new ApcuAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(ApcuAdapter::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var MarshallerInterface $marshaller */
        $marshaller = $this->getMockByCalls(MarshallerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
            Call::create('has')->with('marshaller')->willReturn(true),
            Call::create('get')->with('marshaller')->willReturn($marshaller),
        ]);

        $factory = [ApcuAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ApcuAdapter::class, $service);
    }
}
