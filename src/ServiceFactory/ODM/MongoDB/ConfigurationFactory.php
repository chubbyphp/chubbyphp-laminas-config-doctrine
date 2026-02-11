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
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $mongodbOdm */
        $mongodbOdm = $doctrine['mongodbOdm'] ?? [];

        /** @var array<string, mixed> $configurationConfig */
        $configurationConfig = $mongodbOdm['configuration'] ?? [];

        $config = $this->resolveConfig($configurationConfig);

        /** @var list<array{name: string, className: class-string, parameters: array<string, mixed>}> $filters */
        $filters = $this->resolveValue($container, $config['filters'] ?? []);

        unset($config['filters']);

        $configuration = new Configuration();

        $this->callAdders($configuration, $filters);
        $this->callSetters($container, $configuration, $config);

        return $configuration;
    }

    /**
     * @param list<array{name: string, className: class-string, parameters: array<string, mixed>}> $filters
     */
    private function callAdders(Configuration $configuration, array $filters): void
    {
        foreach ($filters as $filter) {
            $configuration->addFilter($filter['name'], $filter['className'], $filter['parameters']);
        }
    }
}
