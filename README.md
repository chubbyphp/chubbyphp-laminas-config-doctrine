# chubbyphp-laminas-config-doctrine

[![CI](https://github.com/chubbyphp/chubbyphp-laminas-config-doctrine/workflows/CI/badge.svg?branch=master)](https://github.com/chubbyphp/chubbyphp-laminas-config-doctrine/actions?query=workflow%3ACI)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-laminas-config-doctrine/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-laminas-config-doctrine?branch=master)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/chubbyphp/chubbyphp-laminas-config-doctrine/master)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/chubbyphp-laminas-config-doctrine/master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-laminas-config-doctrine/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine)
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

Doctrine service factories for the [laminas/laminas-servicemanager][2] and any other dependency injection container
who's been able to handle it's config, like [chubbyphp/chubbyphp-container][3] via [chubbyphp/chubbyphp-laminas-config][4]
and many (Aura.Di, Pimple, Auryn, Symfony, PHP-DI) more.

The original concept of this service factories is by [@DASPRiD][5] used in [dasprid/container-interop-doctrine][6]
which was handed over to [roave/psr-container-doctrine][7].

Small adjustments like using class names instead `doctrine.something...` strings as service names
and the possiblity to install only the needed vendors make the difference to the original project.

## Requirements

 * php: ^7.4|8.0
 * [chubbyphp/chubbyphp-laminas-config-factory][10]: ^1.0
 * [doctrine/cache][11]: ^1.12.1
 * [doctrine/common][12]: ^3.2
 * [doctrine/event-manager][13]: ^1.1.1
 * [psr/container][14]: ^1.0|^2.0
 * [symfony/console][15]: ^4.4.34|^5.3.11|^6.0.0

## Suggested

 * [doctrine/dbal][20]: ^2.13.1
 * [doctrine/mongodb-odm][21]: ^2.2.1
 * [doctrine/orm][22]: ^2.9.1
 * [mongodb/mongodb][23]: ^1.5.2

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-laminas-config-doctrine][1].

```sh
composer require chubbyphp/chubbyphp-laminas-config-doctrine "^1.2"
```

## Usage

### MongodbODM

 * [Single connection][30]
 * [Multiple connection][31]

### ORM

 * [Single connection][32]
 * [Multiple connection][33]

## Copyright

Dominik Zogg 2021

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-doctrine

[2]: https://packagist.org/packages/laminas/laminas-servicemanager
[3]: https://packagist.org/packages/chubbyphp/chubbyphp-container
[4]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config
[5]: https://github.com/DASPRiD
[6]: https://packagist.org/packages/dasprid/container-interop-doctrine
[7]: https://packagist.org/packages/roave/psr-container-doctrine

[10]: https://packagist.org/packages/chubbyphp/chubbyphp-laminas-config-factory
[11]: https://packagist.org/packages/doctrine/cache
[12]: https://packagist.org/packages/doctrine/common
[13]: https://packagist.org/packages/doctrine/event-manager
[14]: https://packagist.org/packages/psr/container
[15]: https://packagist.org/packages/symfony/console


[20]: https://packagist.org/packages/doctrine/dbal
[21]: https://packagist.org/packages/doctrine/mongodb-odm
[22]: https://packagist.org/packages/doctrine/orm
[23]: https://packagist.org/packages/mongodb/mongodb

[30]: doc/DocumentManager.md
[31]: doc/DocumentManagers.md
[32]: doc/EntityManager.md
[33]: doc/EntityManagers.md
