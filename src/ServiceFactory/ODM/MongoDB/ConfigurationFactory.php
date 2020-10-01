<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\ODM\MongoDB\Configuration;
use Psr\Container\ContainerInterface;

final class ConfigurationFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): Configuration
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['mongodbOdm']['configuration'] ?? []);

        $configuration = new Configuration();

        $this->callAdders($container, $configuration, $config);
        $this->callSetters($container, $configuration, $config);

        return $configuration;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function callAdders(ContainerInterface $container, Configuration $configuration, array &$config): void
    {
        $filters = $this->resolveValue($container, $config['filters'] ?? []);

        unset($config['filters']);

        foreach ($filters as $filter) {
            $configuration->addFilter($filter['name'], $filter['className'], $filter['parameters']);
        }
    }
}
