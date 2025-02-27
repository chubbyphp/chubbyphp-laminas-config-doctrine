<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\PhpFilesAdapterFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvokeWithDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], []),
        ]);

        $factory = new PhpFilesAdapterFactory();

        $service = $factory($container);

        self::assertInstanceOf(PhpFilesAdapter::class, $service);
    }

    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
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
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
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
