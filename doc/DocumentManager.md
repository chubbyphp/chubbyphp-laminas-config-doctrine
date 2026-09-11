# DocumentManager

A single Doctrine MongoDB ODM document manager backed by one MongoDB client.

Only three services need to be registered: the metadata cache, the mapping driver and the document manager itself.
The `DocumentManagerFactory` creates the MongoDB `Client`, the ODM `Configuration` and the `EventManager` on its own
from the `doctrine` configuration, unless you register them explicitly.

The example uses the `ClassMapDriver`, which maps each document class to a small mapping class. Any other Doctrine
mapping driver (attributes, XML, ...) can be used instead by registering it as `MappingDriver::class`.

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
            CacheItemPoolInterface::class => ArrayAdapterFactory::class,
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
                'defaultDB' => 'sample',
                'hydratorDir' => '/tmp/doctrine/mongodbOdm/hydrators',
                'hydratorNamespace' => 'DoctrineMongoDBODMHydrators',
                'metadataCache' => CacheItemPoolInterface::class,
                'metadataDriverImpl' => MappingDriver::class,
                'proxyDir' => '/tmp/doctrine/mongodbOdm/proxies',
                'proxyNamespace' => 'DoctrineMongoDBODMProxy',
            ],
        ],
    ],
];

$factory = new ContainerFactory();

$container = $factory(new Config($config));

/** @var DocumentManager $documentManager */
$documentManager = $container->get(DocumentManager::class);
```

A mapping class for the `ClassMapDriver` implements `ClassMapMappingInterface` and configures the metadata with the
regular Doctrine API:

```php
<?php

declare(strict_types=1);

namespace MyProject\Mapping\MongodbOdm;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as MongodbODMClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class SampleMapping implements ClassMapMappingInterface
{
    /**
     * @param MongodbODMClassMetadata $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $metadata->setCollection('sample');
        $metadata->mapField(['name' => 'id', 'id' => true, 'strategy' => 'none']);
        $metadata->mapField(['name' => 'name', 'type' => Type::STRING]);
    }
}
```
