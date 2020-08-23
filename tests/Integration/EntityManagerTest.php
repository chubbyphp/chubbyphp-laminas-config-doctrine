<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Laminas\Config\Doctrine\Integration;

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayCacheFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\PHPDriverFactory;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class EntityManagerTest extends TestCase
{
    public function testWithoutName(): void
    {
        $config = [
            'dependencies' => [
                'factories' => [
                    Cache::class => ArrayCacheFactory::class,
                    EntityManager::class => EntityManagerFactory::class,
                    MappingDriver::class => PHPDriverFactory::class,
                    'Listener' => static function () {
                        return new \stdClass();
                    },
                    EventSubscriber::class => static function () {
                        return new class() implements EventSubscriber {
                            public function getSubscribedEvents()
                            {
                                return [
                                    Events::postPersist,
                                ];
                            }
                        };
                    },
                ],
            ],
            'doctrine' => [
                'cache' => [
                    'array' => [
                        'namespace' => 'doctrine',
                    ],
                ],
                'dbal' => [
                    'connection' => [
                        'driver' => 'pdo_mysql',
                        'charset' => 'utf8mb4',
                        'user' => 'root',
                        'password' => 'root',
                        'host' => 'localhost',
                        'port' => 3306,
                        'dbname' => 'mydb',
                    ],
                ],
                'driver' => [
                    'phpDriver' => [
                        'locator' => '/doctrine/orm/mappings',
                    ],
                ],
                'eventManager' => [
                    'listeners' => [
                        ['events' => [Events::prePersist], 'listener' => 'Listener'],
                    ],
                    'subscribers' => [
                        EventSubscriber::class,
                    ],
                ],
                'orm' => [
                    'configuration' => [
                        'metadataDriverImpl' => MappingDriver::class,
                        'proxyDir' => '/tmp/doctrine/orm/proxies',
                        'proxyNamespace' => 'DoctrineORMProxy',
                        'metadataCacheImpl' => Cache::class,
                    ],
                ],
            ],
        ];

        $factory = new ContainerFactory();

        $container = $factory(new Config($config));

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        self::assertInstanceOf(EntityManager::class, $entityManager);

        /** @var ArrayCache $metadataCacheImpl */
        $metadataCacheImpl = $entityManager->getConfiguration()->getMetadataCacheImpl();

        self::assertInstanceOf(ArrayCache::class, $metadataCacheImpl);

        self::assertSame('doctrine', $metadataCacheImpl->getNamespace());

        $eventManager = $entityManager->getEventManager();

        $listeners = $eventManager->getListeners();

        self::assertCount(2, $listeners);

        self::assertInstanceOf(\stdClass::class, array_shift($listeners['prePersist']));
        self::assertInstanceOf(EventSubscriber::class, array_shift($listeners['postPersist']));
    }

    public function testWithName(): void
    {
        $config = [
            'dependencies' => [
                'factories' => [
                    Cache::class.'read' => [ArrayCacheFactory::class, 'read'],
                    Cache::class.'write' => [ArrayCacheFactory::class, 'write'],
                    EntityManager::class.'read' => [EntityManagerFactory::class, 'read'],
                    EntityManager::class.'write' => [EntityManagerFactory::class, 'write'],
                    MappingDriver::class.'read' => PHPDriverFactory::class,
                    MappingDriver::class.'write' => PHPDriverFactory::class,
                    'Listenerread' => static function () {
                        return new \stdClass();
                    },
                    'Listenerwrite' => static function () {
                        return new \stdClass();
                    },
                    EventSubscriber::class.'read' => static function () {
                        return new class() implements EventSubscriber {
                            public function getSubscribedEvents()
                            {
                                return [
                                    Events::postPersist,
                                ];
                            }
                        };
                    },
                    EventSubscriber::class.'write' => static function () {
                        return new class() implements EventSubscriber {
                            public function getSubscribedEvents()
                            {
                                return [
                                    Events::postRemove,
                                ];
                            }
                        };
                    },
                ],
            ],
            'doctrine' => [
                'cache' => [
                    'array' => [
                        'read' => [
                            'namespace' => 'doctrine-read',
                        ],
                        'write' => [
                            'namespace' => 'doctrine-write',
                        ],
                    ],
                ],
                'dbal' => [
                    'connection' => [
                        'read' => [
                            'driver' => 'pdo_mysql',
                            'charset' => 'utf8mb4',
                            'user' => 'root',
                            'password' => 'root',
                            'host' => 'localhost',
                            'port' => 3306,
                            'dbname' => 'mydb_read',
                        ],
                        'write' => [
                            'driver' => 'pdo_mysql',
                            'charset' => 'utf8mb4',
                            'user' => 'root',
                            'password' => 'root',
                            'host' => 'localhost',
                            'port' => 3306,
                            'dbname' => 'mydb_write',
                        ],
                    ],
                ],
                'driver' => [
                    'phpDriver' => [
                        'read' => [
                            'locator' => '/doctrine/orm/read/mappings',
                        ],
                        'write' => [
                            'locator' => '/doctrine/orm/write/mappings',
                        ],
                    ],
                ],
                'eventManager' => [
                    'read' => [
                        'listeners' => [
                            ['events' => [Events::prePersist], 'listener' => 'Listenerread'],
                        ],
                        'subscribers' => [
                            EventSubscriber::class.'read',
                        ],
                    ],
                    'write' => [
                        'listeners' => [
                            ['events' => [Events::preRemove], 'listener' => 'Listenerwrite'],
                        ],
                        'subscribers' => [
                            EventSubscriber::class.'write',
                        ],
                    ],
                ],
                'orm' => [
                    'configuration' => [
                        'read' => [
                            'metadataCacheImpl' => Cache::class.'read',
                            'metadataDriverImpl' => MappingDriver::class.'read',
                            'proxyDir' => '/tmp/doctrine/orm/proxies',
                            'proxyNamespace' => 'DoctrineORMProxy',
                        ],
                        'write' => [
                            'metadataCacheImpl' => Cache::class.'write',
                            'metadataDriverImpl' => MappingDriver::class.'write',
                            'proxyDir' => '/tmp/doctrine/orm/proxies',
                            'proxyNamespace' => 'DoctrineORMProxy',
                        ],
                    ],
                ],
            ],
        ];

        $factory = new ContainerFactory();

        $container = $factory(new Config($config));

        /** @var EntityManager $entityManagerRead */
        $entityManagerRead = $container->get(EntityManager::class.'read');

        self::assertInstanceOf(EntityManager::class, $entityManagerRead);

        /** @var ArrayCache $metadataCacheImplRead */
        $metadataCacheImplRead = $entityManagerRead->getConfiguration()->getMetadataCacheImpl();

        self::assertInstanceOf(ArrayCache::class, $metadataCacheImplRead);

        self::assertSame('doctrine-read', $metadataCacheImplRead->getNamespace());

        $eventManagerRead = $entityManagerRead->getEventManager();

        $listenersRead = $eventManagerRead->getListeners();

        self::assertCount(2, $listenersRead);

        self::assertInstanceOf(\stdClass::class, array_shift($listenersRead['prePersist']));
        self::assertInstanceOf(EventSubscriber::class, array_shift($listenersRead['postPersist']));

        /** @var EntityManager $entityManagerWrite */
        $entityManagerWrite = $container->get(EntityManager::class.'write');

        self::assertInstanceOf(EntityManager::class, $entityManagerWrite);

        /** @var ArrayCache $metadataCacheImplWrite */
        $metadataCacheImplWrite = $entityManagerWrite->getConfiguration()->getMetadataCacheImpl();

        self::assertInstanceOf(ArrayCache::class, $metadataCacheImplWrite);

        self::assertSame('doctrine-write', $metadataCacheImplWrite->getNamespace());

        $eventManagerWrite = $entityManagerWrite->getEventManager();

        $listenersWrite = $eventManagerWrite->getListeners();

        self::assertCount(2, $listenersWrite);

        self::assertInstanceOf(\stdClass::class, array_shift($listenersWrite['preRemove']));
        self::assertInstanceOf(EventSubscriber::class, array_shift($listenersWrite['postRemove']));
    }
}
