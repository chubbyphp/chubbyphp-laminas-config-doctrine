# EntityManager

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
            Cache::class => ArrayCacheFactory::class,
            EntityManager::class => EntityManagerFactory::class,
            MappingDriver::class => ClassMapDriverFactory::class,
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
                'driver' => 'pdo_pgsql',
                'url' => 'pgsql://root:root@localhost:5432/sample?charset=utf8',
            ],
        ],
        'driver' => [
            'classMap' => [
                'map' => [
                    Sample::class => SampleMapping::class,
                ],
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
```
