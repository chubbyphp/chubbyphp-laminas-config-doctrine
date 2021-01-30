# DocumentManager

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
            Cache::class => ArrayCacheFactory::class,
            DocumentManager::class => DocumentManagerFactory::class,
            MappingDriver::class => ClassMapDriverFactory::class,
        ],
    ],
    'doctrine' => [
        'cache' => [
            'array' => [
                'namespace' => 'doctrine',
            ],
        ],
        'driver' => [
            'classMap' => [
                'map' => [
                    Sample::class => SampleMapping::class,
                ],
            ],
        ],
        'mongodb' => [
            'client' => [
                'uri' => 'mongodb://root:root@127.0.0.1:27017',
                'driverOptions' => [
                    'typeMap' => DocumentManager::CLIENT_TYPEMAP,
                    'driver' => [
                        'name' => 'doctrine-odm',
                    ],
                ],
            ],
        ],
        'mongodbOdm' => [
            'configuration' => [
                'metadataDriverImpl' => MappingDriver::class,
                'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                'proxyNamespace' => 'DoctrineMongoDBODMProxy',
                'hydratorDir' => '/tmp/doctrine/mongodbOdm/hydrators',
                'hydratorNamespace' => 'DoctrineMongoDBODMHydrators',
                'metadataCacheImpl' => Cache::class,
                'defaultDB' => 'sample',
            ],
        ],
    ],
];

$factory = new ContainerFactory();

$container = $factory(new Config($config));

/** @var DocumentManager $documentManager */
$documentManager = $container->get(DocumentManager::class);
```
