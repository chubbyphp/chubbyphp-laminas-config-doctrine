<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\PHPDriverFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'driver' => [
                        'phpDriver' => ['locator' => '/path/to/mapping/files'],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('/path/to/mapping/files')->willReturn(false),
        ]);

        $factory = new PHPDriverFactory();

        $service = $factory($container);

        self::assertInstanceOf(PHPDriver::class, $service);

        self::assertSame(['/path/to/mapping/files'], $service->getLocator()->getPaths());
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'driver' => [
                        'phpDriver' => [
                            'default' => ['locator' => '/path/to/mapping/files'],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('/path/to/mapping/files')->willReturn(false),
        ]);

        $factory = [PHPDriverFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(PHPDriver::class, $service);

        self::assertSame(['/path/to/mapping/files'], $service->getLocator()->getPaths());
    }
}
