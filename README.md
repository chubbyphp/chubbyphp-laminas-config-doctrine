# chubbyphp-laminas-config-doctrine

[![CI](https://github.com/chubbyphp/chubbyphp-laminas-config-doctrine/actions/workflows/ci.yml/badge.svg)](https://github.com/chubbyphp/chubbyphp-laminas-config-doctrine/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-laminas-config-doctrine/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-laminas-config-doctrine?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchubbyphp%2Fchubbyphp-laminas-config-doctrine%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/chubbyphp-laminas-config-doctrine/master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/v)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/downloads)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)

[![bugs](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=bugs)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![code_smells](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=code_smells)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![coverage](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=coverage)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![duplicated_lines_density](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![ncloc](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=ncloc)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![sqale_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![alert_status](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=alert_status)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![reliability_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![security_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=security_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![sqale_index](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)
[![vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-laminas-config-doctrine&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-laminas-config-doctrine)

## Description

Service factories that wire Doctrine (DBAL, ORM and MongoDB ODM) into a PSR-11 container from plain array
configuration. They follow the [laminas/laminas-servicemanager][2] `dependencies` convention, so they work with
[chubbyphp/chubbyphp-container][3] through [chubbyphp/chubbyphp-laminas-config][4], with Mezzio, and with any other
container that understands the same configuration format (Aura.Di, Pimple, Auryn, Symfony, PHP-DI, ...).

What you get:

 * Factories for the DBAL `Connection`, the ORM `EntityManager`, the MongoDB `Client` and the ODM `DocumentManager`,
   plus their `Configuration` and `EventManager` objects.
 * Factories for the Symfony cache adapters Doctrine uses for metadata, query and result caching
   (array, APCu, chain, filesystem, memcached, PHP files, redis).
 * A `ClassMapDriver` that lets you write mappings as small PHP classes instead of attributes, XML or YAML.
 * Factories for the Doctrine console commands (schema tool, cache clearing, proxies, hydrators, ...) and
   `dbal:database:create` / `dbal:database:drop` commands.
 * Support for multiple named connections, entity managers and document managers (for example `read` and `write`)
   from the same set of factories.

Services are registered under their class name (for example `EntityManagerInterface::class`) rather than under
`doctrine.something` string keys, and only the Doctrine packages you actually use have to be installed.

The concept is based on [dasprid/container-interop-doctrine][6] by [@DASPRiD][5], now maintained as
[roave/psr-container-doctrine][7].

## Requirements

 * php: ^8.3
 * [chubbyphp/chubbyphp-laminas-config-factory][10]: ^1.5.5
 * [doctrine/common][11]: ^3.5
 * [doctrine/event-manager][12]: ^2.1.1
 * [psr/container][13]: ^1.1.2|^2.0.2
 * [symfony/cache][14]: ^6.4.47|^7.4.21|^8.1.9
 * [symfony/console][15]: ^6.4.48|^7.4.21|^8.1.9
 * [symfony/var-exporter][16]: ^6.4.45|^7.4.18

## Suggested

Install only what you need:

 * For the ORM: [doctrine/dbal][20]: ^4.5.0 and [doctrine/orm][22]: ^3.7.5
 * For the MongoDB ODM: [doctrine/mongodb-odm][21]: ^2.17.3 and [mongodb/mongodb][23]: ^2.5.0 (ext-mongodb: ^2.5)

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-laminas-config-doctrine][1].

```sh
composer require chubbyphp/chubbyphp-laminas-config-doctrine "^3.2.0"
```

Then add the Doctrine packages for the storage you use:

```sh
# ORM
composer require doctrine/dbal doctrine/orm

# MongoDB ODM
composer require doctrine/mongodb-odm mongodb/mongodb
```

## Usage

Each factory reads its options from the `doctrine` section of the container configuration and only needs the
`dependencies.factories` entry to be registered. A minimal ORM setup looks like this:

```php
'dependencies' => [
    'factories' => [
        CacheItemPoolInterface::class => ArrayAdapterFactory::class,
        EntityManagerInterface::class => EntityManagerFactory::class,
        MappingDriver::class => ClassMapDriverFactory::class,
    ],
],
'doctrine' => [
    'cache' => ['array' => ['namespace' => 'doctrine']],
    'dbal' => ['connection' => ['driver' => 'pdo_pgsql', 'host' => 'localhost', 'dbname' => 'sample', /* ... */]],
    'driver' => ['classMap' => ['map' => [Sample::class => SampleMapping::class]]],
    'orm' => [
        'configuration' => [
            'metadataCache' => CacheItemPoolInterface::class,
            'metadataDriverImpl' => MappingDriver::class,
            'proxyDir' => '/tmp/doctrine/orm/proxies',
            'proxyNamespace' => 'DoctrineORMProxy',
        ],
    ],
],
```

Good to know:

 * **Configuration values are resolved through the container.** Any string value that matches a registered service
   name is replaced by that service, so `'metadataCache' => CacheItemPoolInterface::class` injects the configured
   cache adapter. Keys under `configuration` map to setters on the Doctrine `Configuration` object
   (`proxyDir` calls `setProxyDir()`), so every option Doctrine offers is available.
 * **Dependencies are resolved lazily.** The `EntityManagerFactory` looks up `Connection::class`,
   `Configuration::class` and `EventManager::class` in the container. If they are not registered, it builds them
   with the matching factory from this package, so you only register what you want to access directly.
 * **Multiple instances share the same factories.** Register a factory as `[EntityManagerFactory::class, 'read']`
   under the service name `EntityManagerInterface::class.'read'`, and nest the related configuration under a `read`
   key. The factory then resolves all of its dependencies with the same `read` suffix.

### MongodbODM

 * [Single connection][30]
 * [Multiple connection][31]

### ORM

 * [Single connection][32]
 * [Multiple connection][33]

### Console commands

Register the command factories from `ServiceFactory\DBAL\Tools\Console\Command`,
`ServiceFactory\ORM\Tools\Console\Command` and `ServiceFactory\ODM\MongoDB\Tools\Console\Command` and add the
resulting commands to your Symfony console application. The commands pick the right instance by name:
`--connection=<name>` for DBAL, `--em=<name>` for the ORM and `--dm=<name>` for the MongoDB ODM. Without the
option the default (unsuffixed) service is used.

## Copyright

2026 Dominik Zogg

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine

[2]: https://packagist.org/packages/laminas/laminas-servicemanager
[3]: https://packagist.org/packages/chubbyphp/chubbyphp-container
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config
[5]: https://github.com/DASPRiD
[6]: https://packagist.org/packages/dasprid/container-interop-doctrine
[7]: https://packagist.org/packages/roave/psr-container-doctrine

[10]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-factory
[11]: https://packagist.org/packages/doctrine/common
[12]: https://packagist.org/packages/doctrine/event-manager
[13]: https://packagist.org/packages/psr/container
[14]: https://packagist.org/packages/symfony/cache
[15]: https://packagist.org/packages/symfony/console
[16]: https://packagist.org/packages/symfony/var-exporter

[20]: https://packagist.org/packages/doctrine/dbal
[21]: https://packagist.org/packages/doctrine/mongodb-odm
[22]: https://packagist.org/packages/doctrine/orm
[23]: https://packagist.org/packages/mongodb/mongodb

[30]: doc/DocumentManager.md
[31]: doc/DocumentManagers.md
[32]: doc/EntityManager.md
[33]: doc/EntityManagers.md
