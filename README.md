# chubbyphp-laminas-config-doctrine

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-laminas-config-doctrine.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-laminas-config-doctrine)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-laminas-config-doctrine/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-laminas-config-doctrine?branch=master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)
[![Daily Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/d/daily)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)

[![bugs](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=bugs)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![code_smells](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=code_smells)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![coverage](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=coverage)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![duplicated_lines_density](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![ncloc](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=ncloc)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![sqale_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![alert_status](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=alert_status)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![reliability_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![security_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=security_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![sqale_index](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)

## Description

Doctrine service factories for the [laminas/laminas-servicemanager][2] and any other dependency injection container
who's been able to handle it's config, like [chubbyphp/chubbyphp-container][3] via [chubbyphp/chubbyphp-laminas-config][4]
and many (Aura.Di, Pimple, Auryn, Symfony, PHP-DI) more.

The original concept of this service factories is by [@DASPRiD][5] used in [dasprid/container-interop-doctrine][6]
which was handed over to [roave/psr-container-doctrine][7].

Small adjustments like using class names instead `doctrine.something...` strings as service names
and the possiblity to install only the needed vendors make the difference to the original project.

## Requirements

 * php: ^7.2
 * [doctrine/cache][10]: ^1.10.2
 * [doctrine/common][11]: ^3.0.2
 * [psr/container][12]: ^1.0

## Suggested

 * [doctrine/dbal][20]: ^2.10.3
 * [doctrine/orm][21]: ^2.7.3

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-laminas-config-doctrine][1].

```sh
composer require chubbyphp/chubbyphp-laminas-config-doctrine "^1.0"
```

## Usage

### Single connection

```php
<?php

declare(strict_types=1);

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayCacheFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\PHPDriverFactory;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\Driver\PHPDriver;

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
                'locator' => '/doctrine/orm/mappings'
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

$entityManager = $container->get(EntityManager::class);
```

### Multiple connections

```php
<?php

declare(strict_types=1);

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayCacheFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\PHPDriverFactory;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\Driver\PHPDriver;

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
                    'locator' => '/doctrine/orm/read/mappings'
                ],
                'write' => [
                    'locator' => '/doctrine/orm/write/mappings'
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

$entityManagerRead = $container->get(EntityManager::class.'read');
$entityManagerWrite = $container->get(EntityManager::class.'write');
```

## Copyright

Dominik Zogg 2020

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine

[2]: https://packagist.org/packages/laminas/laminas-servicemanager
[3]: https://packagist.org/packages/chubbyphp/chubbyphp-container
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config
[5]: https://github.com/DASPRiD
[6]: https://packagist.org/packages/dasprid/container-interop-doctrine
[7]: https://packagist.org/packages/roave/psr-container-doctrine

[10]: https://packagist.org/packages/doctrine/cache
[11]: https://packagist.org/packages/doctrine/common
[12]: https://packagist.org/packages/psr/container

[20]: https://packagist.org/packages/doctrine/dbal
[21]: https://packagist.org/packages/doctrine/orm
