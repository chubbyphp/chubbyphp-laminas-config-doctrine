<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;

final class ConnectionFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): Connection
    {
        /** @var Configuration $configuration */
        $configuration = $this->resolveDependency($container, Configuration::class, ConfigurationFactory::class);

        return DriverManager::getConnection(
            $this->resolveConfig($container->get('config')['doctrine']['dbal']['connection'] ?? []),
            $configuration
        );
    }
}
