# EntityManagers

Two Doctrine ORM entity managers, `read` and `write`, each with its own DBAL connection, cache and mapping driver.

Every factory in this package accepts a name. Register it as `[Factory::class, 'read']` under the service name
suffixed with that same name (`EntityManagerInterface::class.'read'`), and nest the related configuration under a
`read` key. A named factory resolves its dependencies with the same suffix, so the `read` entity manager uses the
`read` connection, the `read` cache and the `read` mapping driver. Any number of names can be used.

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

The Doctrine console commands select an instance with `--em=read` or `--em=write`
(`--connection=<name>` for the DBAL commands).
