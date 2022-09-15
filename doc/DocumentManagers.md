# DocumentManagers

```php
<?php

declare(strict_types=1);

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\DocumentManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
use Chubbyphp\Laminas\Config\Symfony\Component\Cache\Adapter\ArrayAdapterFactory;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use MyProject\Mapping\MongodbOdm\SampleMapping;
use MyProject\Model\Sample;
use Psr\Cache\CacheItemPoolInterface;

$config = [
    'dependencies' => [
        'factories' => [
            CacheItemPoolInterface::class.'read' => [ArrayAdapterFactory::class, 'read'],
            CacheItemPoolInterface::class.'write' => [ArrayAdapterFactory::class, 'write'],
            DocumentManager::class.'read' => [DocumentManagerFactory::class, 'read'],
            DocumentManager::class.'write' => [DocumentManagerFactory::class, 'write'],
            MappingDriver::class.'read' => [ClassMapDriverFactory::class, 'read'],
            MappingDriver::class.'write' => [ClassMapDriverFactory::class, 'write'],
        ],
    ],
    'doctrine' => [
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
                    'uri' => 'mongodb://root:root@127.0.0.1:27017',
                    'driverOptions' => [
                        'typeMap' => DocumentManager::CLIENT_TYPEMAP,
                        'driver' => [
                            'name' => 'doctrine-odm',
                        ],
                    ],
                ],
                'write' => [
                    'uri' => 'mongodb://root:root@127.0.0.1:27017',
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
                    'metadataCache' => CacheItemPoolInterface::class.'write',
                    'metadataDriverImpl' => MappingDriver::class.'write',
                    'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                    'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                ],
                'write' => [
                    'defaultDB' => 'sample_write',
                    'hydratorDir' => '/tmp/doctrine/mongodbOdm/hydrators',
                    'hydratorNamespace' => 'DoctrineMongoDBODMHydrators',
                    'metadataCache' => CacheItemPoolInterface::class.'write',
                    'metadataDriverImpl' => MappingDriver::class.'write',
                    'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                    'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                ],
            ],
        ],
    ],
    'symfony' => [
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
    ]
];

$factory = new ContainerFactory();

$container = $factory(new Config($config));

/** @var DocumentManager $documentManagerRead */
$documentManagerRead = $container->get(DocumentManager::class.'read');

/** @var DocumentManager $documentManagerRead */
$documentManagerRead = $container->get(DocumentManager::class.'write');
```
