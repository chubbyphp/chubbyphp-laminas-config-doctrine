<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\FilesystemCacheFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Cache\FileCache;
use Doctrine\Common\Cache\FilesystemCache;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\FilesystemCacheFactory
 *
 * @internal
 */
final class FilesystemCacheFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'filesystem' => ['namespace' => 'doctrine-cache'],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with(sys_get_temp_dir())->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with(FilesystemCache::EXTENSION)->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = new FilesystemCacheFactory();

        $service = $factory($container);

        self::assertInstanceOf(FilesystemCache::class, $service);

        $umaskReflectionProperty = new \ReflectionProperty(FileCache::class, 'umask');
        $umaskReflectionProperty->setAccessible(true);

        self::assertSame(0002, $umaskReflectionProperty->getValue($service));
    }

    public function testInvoke(): void
    {
        $cacheDirectory = sys_get_temp_dir().'/'.uniqid('doctrine-cache-');

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'filesystem' => [
                            'directory' => $cacheDirectory,
                            'extension' => '.cache',
                            'umask' => 0005,
                            'namespace' => 'doctrine-cache',
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with($cacheDirectory)->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('.cache')->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = new FilesystemCacheFactory();

        $service = $factory($container);

        self::assertInstanceOf(FilesystemCache::class, $service);

        $umaskReflectionProperty = new \ReflectionProperty(FileCache::class, 'umask');
        $umaskReflectionProperty->setAccessible(true);

        self::assertSame(0005, $umaskReflectionProperty->getValue($service));
    }

    public function testCallStatic(): void
    {
        $cacheDirectory = sys_get_temp_dir().'/'.uniqid('doctrine-cache-');

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'filesystem' => [
                            'default' => [
                                'directory' => $cacheDirectory,
                                'extension' => '.cache',
                                'umask' => 0005,
                                'namespace' => 'doctrine-cache',
                            ],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with($cacheDirectory)->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('.cache')->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = [FilesystemCacheFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(FilesystemCache::class, $service);

        $umaskReflectionProperty = new \ReflectionProperty(FileCache::class, 'umask');
        $umaskReflectionProperty->setAccessible(true);

        self::assertSame(0005, $umaskReflectionProperty->getValue($service));
    }
}
