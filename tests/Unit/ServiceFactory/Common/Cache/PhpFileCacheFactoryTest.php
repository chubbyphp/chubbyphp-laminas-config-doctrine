<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\PhpFileCacheFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Cache\FileCache;
use Doctrine\Common\Cache\PhpFileCache;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\PhpFileCacheFactory
 *
 * @internal
 */
final class PhpFileCacheFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'cache' => [
                        'phpFile' => ['namespace' => 'doctrine-cache'],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with(sys_get_temp_dir())->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with(PhpFileCache::EXTENSION)->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = new PhpFileCacheFactory();

        $service = $factory($container);

        self::assertInstanceOf(PhpFileCache::class, $service);

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
                        'phpFile' => [
                            'directory' => $cacheDirectory,
                            'extension' => '.php7',
                            'umask' => 0005,
                            'namespace' => 'doctrine-cache',
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with($cacheDirectory)->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('.php7')->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = new PhpFileCacheFactory();

        $service = $factory($container);

        self::assertInstanceOf(PhpFileCache::class, $service);

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
                        'phpFile' => [
                            'default' => [
                                'directory' => $cacheDirectory,
                                'extension' => '.php7',
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
            Call::create('has')->with('.php7')->willReturn(false),
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with('doctrine-cache')->willReturn(false),
        ]);

        $factory = [PhpFileCacheFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(PhpFileCache::class, $service);

        $umaskReflectionProperty = new \ReflectionProperty(FileCache::class, 'umask');
        $umaskReflectionProperty->setAccessible(true);

        self::assertSame(0005, $umaskReflectionProperty->getValue($service));
    }
}
