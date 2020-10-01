# EntityManagers

```php
<?php

declare(strict_types=1);

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayCacheFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use MyProject\Mapping\Orm\SampleMapping;
use MyProject\Model\Sample;

$config = [
    'dependencies' => [
        'factories' => [
            Cache::class.'read' => [ArrayCacheFactory::class, 'read'],
            Cache::class.'write' => [ArrayCacheFactory::class, 'write'],
            EntityManager::class.'read' => [EntityManagerFactory::class, 'read'],
            EntityManager::class.'write' => [EntityManagerFactory::class, 'write'],
            MappingDriver::class.'read' => [ClassMapDriverFactory::class, 'read'],
            MappingDriver::class.'write' => [ClassMapDriverFactory::class, 'write'],
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
                    'dbname' => 'sample_read',
                ],
                'write' => [
                    'driver' => 'pdo_mysql',
                    'charset' => 'utf8mb4',
                    'user' => 'root',
                    'password' => 'root',
                    'host' => 'localhost',
                    'port' => 3306,
                    'dbname' => 'sample_write',
                ],
            ],
        ],
        'driver' => [
            'classMap' => [
                'read' => [
                    'map' => [
                        Sample::class => SampleMapping::class,
                    ],
                ],
                'write' => [
                    'map' => [
                        Sample::class => SampleMapping::class,
                    ],
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

/** @var EntityManager $entityManagerWrite */
$entityManagerWrite = $container->get(EntityManager::class.'write');
```
