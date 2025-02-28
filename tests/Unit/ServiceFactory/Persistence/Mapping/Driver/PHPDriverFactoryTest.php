<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\PHPDriverFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\Persistence\Mapping\Driver\PHPDriver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\PHPDriverFactory
 *
 * @internal
 */
final class PHPDriverFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'driver' => [
                        'phpDriver' => ['locator' => '/path/to/mapping/files'],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['/path/to/mapping/files'], false),
        ]);

        $factory = new PHPDriverFactory();

        $service = $factory($container);

        self::assertInstanceOf(PHPDriver::class, $service);

        self::assertSame(['/path/to/mapping/files'], $service->getLocator()->getPaths());
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'driver' => [
                        'phpDriver' => [
                            'default' => ['locator' => '/path/to/mapping/files'],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['/path/to/mapping/files'], false),
        ]);

        $factory = [PHPDriverFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(PHPDriver::class, $service);

        self::assertSame(['/path/to/mapping/files'], $service->getLocator()->getPaths());
    }
}
