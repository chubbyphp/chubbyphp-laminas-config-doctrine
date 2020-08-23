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
        $configuration = new Configuration();

        $this->callSetters(
            $container,
            $configuration,
            $this->resolveConfig($container->get('config')['doctrine']['dbal']['configuration'] ?? [])
        );

        return $configuration;
    }
}
