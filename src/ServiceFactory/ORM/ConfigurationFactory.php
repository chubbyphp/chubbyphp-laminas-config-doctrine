<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\ORM\Configuration;
use Psr\Container\ContainerInterface;

final class ConfigurationFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): Configuration
    {
        $configuration = new Configuration();

        $this->callSetters(
            $container,
            $configuration,
            $this->resolveConfig($container->get('config')['doctrine']['orm']['configuration'] ?? [])
        );

        return $configuration;
    }
}
