<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ODM\MongoDB;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\ConfigurationFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\ConfigurationFactory
 *
 * @internal
 */
final class ConfigurationFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $builder->create(MappingDriver::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'mongodbOdm' => [
                        'configuration' => [
                            'metadataDriverImpl' => MappingDriver::class,
                            'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                            'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                            'filters' => [
                                ['name' => 'filter', 'className' => 'className', 'parameters' => []],
                            ],
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', ['filter'], false),
            new WithReturn('has', ['className'], false),
            new WithReturn('has', [MappingDriver::class], true),
            new WithReturn('get', [MappingDriver::class], $mappingDriver),
            new WithReturn('has', ['/tmp/doctrine/mongodbOdm/proxies'], false),
            new WithReturn('has', ['DoctrineMongoDBODMProxy'], false),
        ]);

        $factory = new ConfigurationFactory();

        /** @var Configuration $service */
        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);

        self::assertSame('className', $service->getFilterClassName('filter'));
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $builder->create(MappingDriver::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'mongodbOdm' => [
                        'configuration' => [
                            'default' => [
                                'metadataDriverImpl' => MappingDriver::class,
                                'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                                'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                                'filters' => [
                                    ['name' => 'filter', 'className' => 'className', 'parameters' => []],
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', ['filter'], false),
            new WithReturn('has', ['className'], false),
            new WithReturn('has', [MappingDriver::class], true),
            new WithReturn('get', [MappingDriver::class], $mappingDriver),
            new WithReturn('has', ['/tmp/doctrine/mongodbOdm/proxies'], false),
            new WithReturn('has', ['DoctrineMongoDBODMProxy'], false),
        ]);

        $factory = [ConfigurationFactory::class, 'default'];

        /** @var Configuration $service */
        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);

        self::assertSame('className', $service->getFilterClassName('filter'));
    }
}
