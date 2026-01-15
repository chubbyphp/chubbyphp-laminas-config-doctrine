<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\DBAL\Configuration;
use Psr\Container\ContainerInterface;

final class ConfigurationFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): Configuration
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $dbal */
        $dbal = $doctrine['dbal'] ?? [];

        /** @var array<string, mixed> $configurationConfig */
        $configurationConfig = $dbal['configuration'] ?? [];

        $configuration = new Configuration();

        $this->callSetters(
            $container,
            $configuration,
            $this->resolveConfig($configurationConfig)
        );

        return $configuration;
    }
}
