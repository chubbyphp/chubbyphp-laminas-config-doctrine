# EntityManager

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
