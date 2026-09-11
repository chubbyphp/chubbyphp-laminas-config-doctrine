# DocumentManagers

Two Doctrine MongoDB ODM document managers, `read` and `write`, each with its own MongoDB client, cache and mapping
driver.

Every factory in this package accepts a name. Register it as `[Factory::class, 'read']` under the service name
suffixed with that same name (`DocumentManager::class.'read'`), and nest the related configuration under a `read`
key. A named factory resolves its dependencies with the same suffix, so the `read` document manager uses the `read`
client, the `read` cache and the `read` mapping driver. Any number of names can be used.

```php
<?php

declare(strict_types=1);

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayAdapterFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB\DocumentManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver\ClassMapDriverFactory;
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
                    'metadataCache' => CacheItemPoolInterface::class.'read',
                    'metadataDriverImpl' => MappingDriver::class.'read',
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
];

$factory = new ContainerFactory();

$container = $factory(new Config($config));

/** @var DocumentManager $documentManagerRead */
$documentManagerRead = $container->get(DocumentManager::class.'read');

/** @var DocumentManager $documentManagerWrite */
$documentManagerWrite = $container->get(DocumentManager::class.'write');
```

The Doctrine console commands select an instance with `--dm=read` or `--dm=write`.
