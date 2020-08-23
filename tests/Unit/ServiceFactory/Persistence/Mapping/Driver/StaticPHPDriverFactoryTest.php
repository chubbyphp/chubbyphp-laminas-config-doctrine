<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\StaticPHPDriverFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Persistence\Mapping\Driver\StaticPHPDriver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\StaticPHPDriverFactory
 *
 * @internal
 */
final class StaticPHPDriverFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'driver' => [
                        'staticPhpDriver' => ['paths' => '/path/to/classes'],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('/path/to/classes')->willReturn(false),
        ]);

        $factory = new StaticPHPDriverFactory();

        $service = $factory($container);

        self::assertInstanceOf(StaticPHPDriver::class, $service);

        $pathsReflectionProperty = new \ReflectionProperty(StaticPHPDriver::class, 'paths');
        $pathsReflectionProperty->setAccessible(true);

        self::assertSame(['/path/to/classes'], $pathsReflectionProperty->getValue($service));
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'driver' => [
                        'staticPhpDriver' => [
                            'default' => ['paths' => '/path/to/classes'],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('/path/to/classes')->willReturn(false),
        ]);

        $factory = [StaticPHPDriverFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(StaticPHPDriver::class, $service);

        $pathsReflectionProperty = new \ReflectionProperty(StaticPHPDriver::class, 'paths');
        $pathsReflectionProperty->setAccessible(true);

        self::assertSame(['/path/to/classes'], $pathsReflectionProperty->getValue($service));
    }
}
