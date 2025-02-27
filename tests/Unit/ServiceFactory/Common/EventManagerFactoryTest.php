<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\EventManagerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\EventManagerFactory
 *
 * @internal
 */
final class EventManagerFactoryTest extends TestCase
{
    public function testInvokeWithDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'eventManager' => [],
                ],
            ]),
        ]);

        $factory = new EventManagerFactory();

        $service = $factory($container);

        self::assertInstanceOf(EventManager::class, $service);
    }

    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        $listener = new \stdClass();

        $subscriber = new class implements EventSubscriber {
            public function getSubscribedEvents()
            {
                return [
                    Events::postPersist,
                ];
            }
        };

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'eventManager' => [
                        'listeners' => [
                            ['events' => [Events::prePersist], 'listener' => 'Listener'],
                        ],
                        'subscribers' => [
                            EventSubscriber::class,
                        ],
                    ],
                ],
            ]),
            // Simulate resolveValue for string values
            new WithReturn('has', [Events::prePersist], false),
            new WithReturn('has', ['Listener'], true),
            new WithReturn('get', ['Listener'], $listener),
            new WithReturn('has', [EventSubscriber::class], true),
            new WithReturn('get', [EventSubscriber::class], $subscriber),
        ]);

        $factory = new EventManagerFactory();

        $service = $factory($container);

        self::assertInstanceOf(EventManager::class, $service);

        $prePersistListeners = $service->getListeners(Events::prePersist);
        $postPersistListeners = $service->getListeners(Events::postPersist);

        self::assertCount(1, $prePersistListeners);
        self::assertCount(1, $postPersistListeners);

        self::assertInstanceOf(\stdClass::class, array_shift($prePersistListeners));
        self::assertInstanceOf(EventSubscriber::class, array_shift($postPersistListeners));
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        $listener = new \stdClass();

        $subscriber = new class implements EventSubscriber {
            public function getSubscribedEvents()
            {
                return [
                    Events::postPersist,
                ];
            }
        };

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'doctrine' => [
                    'eventManager' => [
                        'default' => [
                            'listeners' => [
                                ['events' => [Events::prePersist], 'listener' => 'Listener'],
                            ],
                            'subscribers' => [
                                EventSubscriber::class,
                            ],
                        ],
                    ],
                ],
            ]),
            new WithReturn('has', [Events::prePersist], false),
            new WithReturn('has', ['Listener'], true),
            new WithReturn('get', ['Listener'], $listener),
            new WithReturn('has', [EventSubscriber::class], true),
            new WithReturn('get', [EventSubscriber::class], $subscriber),
        ]);

        $factory = [EventManagerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(EventManager::class, $service);

        $prePersistListeners = $service->getListeners(Events::prePersist);
        $postPersistListeners = $service->getListeners(Events::postPersist);

        self::assertCount(1, $prePersistListeners);
        self::assertCount(1, $postPersistListeners);

        self::assertInstanceOf(\stdClass::class, array_shift($prePersistListeners));
        self::assertInstanceOf(EventSubscriber::class, array_shift($postPersistListeners));
    }
}
