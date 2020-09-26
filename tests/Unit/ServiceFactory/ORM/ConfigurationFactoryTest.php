<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\ConfigurationFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Query\ResultSetMapping;
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

        /** @var ResultSetMapping $resultSetMapping */
        $resultSetMapping = $this->getMockByCalls(ResultSetMapping::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'orm' => [
                        'configuration' => [
                            'metadataDriverImpl' => MappingDriver::class,
                            'proxyDir' => '/tmp/doctrine/orm/proxies',
                            'proxyNamespace' => 'DoctrineORMProxy',
                            'namedQueries' => [
                                ['name' => 'namedQuery', 'dql' => 'dql'],
                            ],
                            'namedNativeQueries' => [
                                ['name' => 'namedNativeQuery', 'sql' => 'sql', 'rsm' => 'rsm'],
                            ],
                            'filters' => [
                                ['name' => 'filter', 'className' => 'className'],
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with('namedQuery')->willReturn(false),
            Call::create('has')->with('dql')->willReturn(false),
            Call::create('has')->with('namedNativeQuery')->willReturn(false),
            Call::create('has')->with('sql')->willReturn(false),
            Call::create('has')->with('rsm')->willReturn(true),
            Call::create('get')->with('rsm')->willReturn($resultSetMapping),
            Call::create('has')->with('filter')->willReturn(false),
            Call::create('has')->with('className')->willReturn(false),
            Call::create('has')->with(MappingDriver::class)->willReturn(true),
            Call::create('get')->with(MappingDriver::class)->willReturn($mappingDriver),
            Call::create('has')->with('/tmp/doctrine/orm/proxies')->willReturn(false),
            Call::create('has')->with('DoctrineORMProxy')->willReturn(false),
        ]);

        $factory = new ConfigurationFactory();

        /** @var Configuration $service */
        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);

        self::assertSame('dql', $service->getNamedQuery('namedQuery'));
        self::assertSame(['sql', $resultSetMapping], $service->getNamedNativeQuery('namedNativeQuery'));
        self::assertSame('className', $service->getFilterClassName('filter'));
    }

    public function testCallStatic(): void
    {
        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $this->getMockByCalls(MappingDriver::class);

        /** @var ResultSetMapping $resultSetMapping */
        $resultSetMapping = $this->getMockByCalls(ResultSetMapping::class);

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
                                'namedQueries' => [
                                    ['name' => 'namedQuery', 'dql' => 'dql'],
                                ],
                                'namedNativeQueries' => [
                                    ['name' => 'namedNativeQuery', 'sql' => 'sql', 'rsm' => 'rsm'],
                                ],
                                'filters' => [
                                    ['name' => 'filter', 'className' => 'className'],
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with('namedQuery')->willReturn(false),
            Call::create('has')->with('dql')->willReturn(false),
            Call::create('has')->with('namedNativeQuery')->willReturn(false),
            Call::create('has')->with('sql')->willReturn(false),
            Call::create('has')->with('rsm')->willReturn(true),
            Call::create('get')->with('rsm')->willReturn($resultSetMapping),
            Call::create('has')->with('filter')->willReturn(false),
            Call::create('has')->with('className')->willReturn(false),
            Call::create('has')->with(MappingDriver::class)->willReturn(true),
            Call::create('get')->with(MappingDriver::class)->willReturn($mappingDriver),
            Call::create('has')->with('/tmp/doctrine/orm/proxies')->willReturn(false),
            Call::create('has')->with('DoctrineORMProxy')->willReturn(false),
        ]);

        $factory = [ConfigurationFactory::class, 'default'];

        /** @var Configuration $service */
        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);

        self::assertSame('dql', $service->getNamedQuery('namedQuery'));
        self::assertSame(['sql', $resultSetMapping], $service->getNamedNativeQuery('namedNativeQuery'));
        self::assertSame('className', $service->getFilterClassName('filter'));
    }
}
