# EntityManagers

```php
<?php

declare(strict_types=1);

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayAdapterFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\EntityManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use MyProject\Mapping\Orm\SampleMapping;
use MyProject\Model\Sample;
use Psr\Cache\CacheItemPoolInterface;

$dsnParser = new DsnParser();
$connectionParamsRead = $dsnParser->parse('pgsql://root:root@localhost:5432/sample_read?charset=utf8');
$connectionParamsWrite = $dsnParser->parse('pgsql://root:root@localhost:5432/sample_write?charset=utf8');

$config = [
    'dependencies' => [
        'factories' => [
            CacheItemPoolInterface::class.'read' => [ArrayAdapterFactory::class, 'read'],
            CacheItemPoolInterface::class.'write' => [ArrayAdapterFactory::class, 'write'],
            EntityManagerInterface::class.'read' => [EntityManagerFactory::class, 'read'],
            EntityManagerInterface::class.'write' => [EntityManagerFactory::class, 'write'],
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
                'read' => $connectionParamsRead,
                'write' => $connectionParamsWrite,
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
                    'metadataCache' => CacheItemPoolInterface::class.'read',
                    'metadataDriverImpl' => MappingDriver::class.'read',
                    'proxyDir' => '/tmp/doctrine/orm/proxies',
                    'proxyNamespace' => 'DoctrineORMProxy',
                ],
                'write' => [
                    'metadataCache' => CacheItemPoolInterface::class.'write',
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

/** @var EntityManagerInterface $entityManagerRead */
$entityManagerRead = $container->get(EntityManagerInterface::class.'read');

/** @var EntityManagerInterface $entityManagerWrite */
$entityManagerWrite = $container->get(EntityManagerInterface::class.'write');
```
