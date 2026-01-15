<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;

final class ConnectionFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): Connection
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $dbal */
        $dbal = $doctrine['dbal'] ?? [];

        /** @var array<string, mixed> $connectionConfig */
        $connectionConfig = $dbal['connection'] ?? [];

        /** @var Configuration $configuration */
        $configuration = $this->resolveDependency($container, Configuration::class, ConfigurationFactory::class);

        /** @var array{driver?: 'ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv', driverClass?: class-string<Driver>, host?: string, port?: int, dbname?: string, user?: string, password?: string, charset?: string, path?: string, url?: string, driverOptions?: array<mixed>, ...} $connectionParams */
        $connectionParams = $this->resolveConfig($connectionConfig);

        return DriverManager::getConnection(
            $connectionParams,
            $configuration
        );
    }
}
