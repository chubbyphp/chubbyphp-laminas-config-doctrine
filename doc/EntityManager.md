# EntityManager

A single Doctrine ORM entity manager backed by one DBAL connection.

Only three services need to be registered: the metadata cache, the mapping driver and the entity manager itself.
The `EntityManagerFactory` creates the DBAL `Connection`, the ORM `Configuration` and the `EventManager` on its own
from the `doctrine` configuration, unless you register them explicitly.

The example uses the `ClassMapDriver`, which maps each entity class to a small mapping class. Any other Doctrine
mapping driver (attributes, XML, ...) can be used instead by registering it as `MappingDriver::class`.

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

$dsnParser = new DsnParser(['pgsql' => 'pdo_pgsql']);
$connectionParams = $dsnParser->parse('pgsql://root:root@localhost:5432/sample?charset=utf8');

$config = [
    'dependencies' => [
        'factories' => [
            CacheItemPoolInterface::class => ArrayAdapterFactory::class,
            EntityManagerInterface::class => EntityManagerFactory::class,
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
            'connection' => $connectionParams,
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
                'metadataCache' => CacheItemPoolInterface::class,
                'metadataDriverImpl' => MappingDriver::class,
                'proxyDir' => '/tmp/doctrine/orm/proxies',
                'proxyNamespace' => 'DoctrineORMProxy',
            ],
        ],
    ],
];

$factory = new ContainerFactory();

$container = $factory(new Config($config));

/** @var EntityManagerInterface $entityManager */
$entityManager = $container->get(EntityManagerInterface::class);
```

A mapping class for the `ClassMapDriver` implements `ClassMapMappingInterface` and configures the metadata with the
regular Doctrine API:

```php
<?php

declare(strict_types=1);

namespace MyProject\Mapping\Orm;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class SampleMapping implements ClassMapMappingInterface
{
    /**
     * @param ORMClassMetadata $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('sample');
        $builder->createField('id', Types::GUID)->makePrimaryKey()->build();
        $builder->addField('name', Types::STRING);
    }
}
```
