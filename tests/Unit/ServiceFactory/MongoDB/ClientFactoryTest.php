<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\MongoDB;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\MongoDB\ClientFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use MongoDB\Client;
use MongoDB\Model\BSONArray;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\MongoDB\ClientFactory
 *
 * @internal
 */
final class ClientFactoryTest extends TestCase
{
    public function testInvokeWithDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'mongodb' => [
                        'client' => [],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['mongodb://127.0.0.1/'], false),
        ]);

        $factory = new ClientFactory();

        $service = $factory($container);

        self::assertInstanceOf(Client::class, $service);
    }

    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'mongodb' => [
                        'client' => [
                            'uri' => 'mongodb://host:9876/',
                            'uriOptions' => [
                                'appname' => 'app',
                            ],
                            'driverOptions' => [
                                'typeMap' => [
                                    'array' => BSONArray::class,
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['mongodb://host:9876/'], false),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['app'], false),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', [BSONArray::class], false),
        ]);

        $factory = new ClientFactory();

        $service = $factory($container);

        self::assertInstanceOf(Client::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'mongodb' => [
                        'client' => [
                            'default' => [
                                'uri' => 'mongodb://host:9876/',
                                'uriOptions' => [
                                    'appname' => 'app',
                                ],
                                'driverOptions' => [
                                    'typeMap' => [
                                        'array' => BSONArray::class,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['mongodb://host:9876/'], false),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', ['app'], false),
            // this is cause each string value could be a service (resolveValue)
            new WithReturn('has', [BSONArray::class], false),
        ]);

        $factory = [ClientFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(Client::class, $service);
    }
}
