<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\ORM;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\ConfigurationFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var MappingDriver $mappingDriver */
        $mappingDriver = $builder->create(MappingDriver::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'orm' => [
                        'configuration' => [
                            'metadataDriverImpl' => MappingDriver::class,
                            'proxyDir' => '/tmp/doctrine/orm/proxies',
                            'proxyNamespace' => 'DoctrineORMProxy',
                            'filters' => [
                                ['name' => 'filter', 'className' => 'className'],
                            ],
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', ['filter'], false),
            new WithReturn('has', ['className'], false),
            new WithReturn('has', [MappingDriver::class], true),
            new WithReturn('get', [MappingDriver::class], $mappingDriver),
            new WithReturn('has', ['/tmp/doctrine/orm/proxies'], false),
            new WithReturn('has', ['DoctrineORMProxy'], false),
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
                    'orm' => [
                        'configuration' => [
                            'default' => [
                                'metadataDriverImpl' => MappingDriver::class,
                                'proxyDir' => '/tmp/doctrine/orm/proxies',
                                'proxyNamespace' => 'DoctrineORMProxy',
                                'filters' => [
                                    ['name' => 'filter', 'className' => 'className'],
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
            new WithReturn('has', ['/tmp/doctrine/orm/proxies'], false),
            new WithReturn('has', ['DoctrineORMProxy'], false),
        ]);

        $factory = [ConfigurationFactory::class, 'default'];

        /** @var Configuration $service */
        $service = $factory($container);

        self::assertInstanceOf(Configuration::class, $service);

        self::assertSame('className', $service->getFilterClassName('filter'));
    }
}
