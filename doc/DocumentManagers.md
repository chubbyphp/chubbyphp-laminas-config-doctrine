# DocumentManagers

```php
<?php

declare(strict_types=1);

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayCacheFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\DocumentManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Doctrine\Common\Cache\Cache;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use MyProject\Mapping\MongodbOdm\SampleMapping;
use MyProject\Model\Sample;

$config = [
    'dependencies' => [
        'factories' => [
            Cache::class.'read' => [ArrayCacheFactory::class, 'read'],
            Cache::class.'write' => [ArrayCacheFactory::class, 'write'],
            DocumentManager::class.'read' => [DocumentManagerFactory::class, 'read'],
            DocumentManager::class.'write' => [DocumentManagerFactory::class, 'write'],
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
        'mongodb' => [
            'client' => [
                'read' => [
                    'uri' => 'mongodb://root:root@127.0.0.1',
                    'driverOptions' => [
                        'typeMap' => DocumentManager::CLIENT_TYPEMAP,
                        'driver' => [
                            'name' => 'doctrine-odm',
                        ],
                    ],
                ],
                'write' => [
                    'uri' => 'mongodb://root:root@127.0.0.1',
                    'driverOptions' => [
                        'typeMap' => DocumentManager::CLIENT_TYPEMAP,
                        'driver' => [
                            'name' => 'doctrine-odm',
                        ],
                    ],
                ],
            ],
        ],
        'mongodbOdm' => [
            'configuration' => [
                'read' => [
                    'defaultDB' => 'sample_read',
                    'hydratorDir' => '/tmp/doctrine/mongodbOdm/hydrators',
                    'hydratorNamespace' => 'DoctrineMongoDBODMHydrators',
                    'metadataCacheImpl' => Cache::class.'write',
                    'metadataDriverImpl' => MappingDriver::class.'write',
                    'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                    'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                ],
                'write' => [
                    'defaultDB' => 'sample_write',
                    'hydratorDir' => '/tmp/doctrine/mongodbOdm/hydrators',
                    'hydratorNamespace' => 'DoctrineMongoDBODMHydrators',
                    'metadataCacheImpl' => Cache::class.'write',
                    'metadataDriverImpl' => MappingDriver::class.'write',
                    'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                    'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                ],
            ],
        ],
    ],
];

$factory = new ContainerFactory();

$container = $factory(new Config($config));

/** @var DocumentManager $documentManagerRead */
$documentManagerRead = $container->get(DocumentManager::class.'read');

/** @var DocumentManager $documentManagerRead */
$documentManagerRead = $container->get(DocumentManager::class.'write');
```
