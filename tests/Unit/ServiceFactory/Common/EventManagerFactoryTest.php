<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Unit\ServiceFactory\Common;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\EventManagerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'doctrine' => [
                    'eventManager' => [],
                ],
            ]),
        ]);

        $factory = new EventManagerFactory();

        $service = $factory($container);

        self::assertInstanceOf(EventManager::class, $service);

        $listeners = $service->getListeners();

        self::assertCount(0, $listeners);
    }

    public function testInvoke(): void
    {
        $listener = new \stdClass();

        $subscriber = new class() implements EventSubscriber {
            public function getSubscribedEvents()
            {
                return [
                    Events::postPersist,
                ];
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with(Events::prePersist)->willReturn(false),
            Call::create('has')->with('Listener')->willReturn(true),
            Call::create('get')->with('Listener')->willReturn($listener),
            Call::create('has')->with(EventSubscriber::class)->willReturn(true),
            Call::create('get')->with(EventSubscriber::class)->willReturn($subscriber),
        ]);

        $factory = new EventManagerFactory();

        $service = $factory($container);

        self::assertInstanceOf(EventManager::class, $service);

        $listeners = $service->getListeners();

        self::assertCount(2, $listeners);

        self::assertInstanceOf(\stdClass::class, array_shift($listeners['prePersist']));
        self::assertInstanceOf(EventSubscriber::class, array_shift($listeners['postPersist']));
    }

    public function testCallStatic(): void
    {
        $listener = new \stdClass();

        $subscriber = new class() implements EventSubscriber {
            public function getSubscribedEvents()
            {
                return [
                    Events::postPersist,
                ];
            }
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
            // this is cause each string value could be a service (resolveValue)
            Call::create('has')->with(Events::prePersist)->willReturn(false),
            Call::create('has')->with('Listener')->willReturn(true),
            Call::create('get')->with('Listener')->willReturn($listener),
            Call::create('has')->with(EventSubscriber::class)->willReturn(true),
            Call::create('get')->with(EventSubscriber::class)->willReturn($subscriber),
        ]);

        $factory = [EventManagerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(EventManager::class, $service);

        $listeners = $service->getListeners();

        self::assertCount(2, $listeners);

        self::assertInstanceOf(\stdClass::class, array_shift($listeners['prePersist']));
        self::assertInstanceOf(EventSubscriber::class, array_shift($listeners['postPersist']));
    }
}
