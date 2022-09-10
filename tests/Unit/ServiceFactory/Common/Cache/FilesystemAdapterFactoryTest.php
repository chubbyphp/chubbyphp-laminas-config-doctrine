<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\FilesystemAdapterFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\FilesystemAdapterFactory
 *
 * @internal
 */
final class FilesystemAdapterFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([]),
        ]);

        $factory = new FilesystemAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(FilesystemAdapter::class, $service);
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
                        'filesystem' => [
                            'namespace' => 'some_namespace',
                            'defaultLifetime' => 120,
                            'directory' => '/path/to/cache',
                            'marshaller' => 'marshaller',
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with('marshaller')->willReturn(true),
            Call::create('get')->with('marshaller')->willReturn($marshaller),
        ]);

        $factory = new FilesystemAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(FilesystemAdapter::class, $service);
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
                        'filesystem' => [
                            'default' => [
                                'namespace' => 'some_namespace',
                                'defaultLifetime' => 120,
                                'directory' => '/path/to/cache',
                                'marshaller' => 'marshaller',
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with('marshaller')->willReturn(true),
            Call::create('get')->with('marshaller')->willReturn($marshaller),
        ]);

        $factory = [FilesystemAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(FilesystemAdapter::class, $service);
    }
}
