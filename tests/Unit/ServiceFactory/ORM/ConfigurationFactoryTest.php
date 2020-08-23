<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\ConfigurationFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Configuration;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\ConfigurationFactory
 *
 * @internal
 */
final class ConfigurationFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $this->getMockByCalls(MappingDriver::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'orm' => [
                        'configuration' => [
                            'metadataDriverImpl' => MappingDriver::class,
                            'proxyDir' => '/tmp/doctrine/orm/proxies',
                            'proxyNamespace' => 'DoctrineORMProxy',
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(MappingDriver::class)->willReturn(true),
            Call::create('get')->with(MappingDriver::class)->willReturn($mappingDriver),
            Call::create('has')->with('/tmp/doctrine/orm/proxies')->willReturn(false),
            Call::create('has')->with('DoctrineORMProxy')->willReturn(false),
        ]);

        $factory = new ConfigurationFactory();

        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $this->getMockByCalls(MappingDriver::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'orm' => [
                        'configuration' => [
                            'default' => [
                                'metadataDriverImpl' => MappingDriver::class,
                                'proxyDir' => '/tmp/doctrine/orm/proxies',
                                'proxyNamespace' => 'DoctrineORMProxy',
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(MappingDriver::class)->willReturn(true),
            Call::create('get')->with(MappingDriver::class)->willReturn($mappingDriver),
            Call::create('has')->with('/tmp/doctrine/orm/proxies')->willReturn(false),
            Call::create('has')->with('DoctrineORMProxy')->willReturn(false),
        ]);

        $factory = [ConfigurationFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);
    }
}
