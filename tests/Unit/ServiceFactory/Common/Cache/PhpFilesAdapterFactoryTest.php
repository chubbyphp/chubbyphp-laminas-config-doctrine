<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\PhpFilesAdapterFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\PhpFilesAdapterFactory
 *
 * @internal
 */
final class PhpFilesAdapterFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([]),
        ]);

        $factory = new PhpFilesAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(PhpFilesAdapter::class, $service);
    }

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'phpfiles' => [
                            'namespace' => 'some_namespace',
                            'defaultLifetime' => 120,
                            'directory' => '/path/to/cache',
                            'appendOnly' => true,
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = new PhpFilesAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(PhpFilesAdapter::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'phpfiles' => [
                            'default' => [
                                'namespace' => 'some_namespace',
                                'defaultLifetime' => 120,
                                'directory' => '/path/to/cache',
                                'appendOnly' => true,
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = [PhpFilesAdapterFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(PhpFilesAdapter::class, $service);
    }
}
