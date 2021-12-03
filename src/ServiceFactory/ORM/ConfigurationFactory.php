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
        $config = $this->resolveConfig($container->get('config')['doctrine']['orm']['configuration'] ?? []);

        /** @var array<string, mixed> $namedQueries */
        $namedQueries = $this->resolveValue($container, $config['namedQueries'] ?? []);

        /** @var array<string, mixed> $namedNativeQueries */
        $namedNativeQueries = $this->resolveValue($container, $config['namedNativeQueries'] ?? []);

        /** @var array<string, mixed> $filters */
        $filters = $this->resolveValue($container, $config['filters'] ?? []);

        unset($config['namedQueries'], $config['namedNativeQueries'], $config['filters']);

        $configuration = new Configuration();

        $this->callAdders($configuration, $namedQueries, $namedNativeQueries, $filters);
        $this->callSetters($container, $configuration, $config);

        return $configuration;
    }

    /**
     * @param array<string, mixed> $namedQueries
     * @param array<string, mixed> $namedNativeQueries
     * @param array<string, mixed> $filters
     */
    private function callAdders(Configuration $configuration, array $namedQueries, array $namedNativeQueries, array $filters): void
    {
        foreach ($namedQueries as $namedQuery) {
            $configuration->addNamedQuery($namedQuery['name'], $namedQuery['dql']);
        }

        foreach ($namedNativeQueries as $namedNativeQuery) {
            $configuration->addNamedNativeQuery(
                $namedNativeQuery['name'],
                $namedNativeQuery['sql'],
                $namedNativeQuery['rsm']
            );
        }

        foreach ($filters as $filter) {
            $configuration->addFilter($filter['name'], $filter['className']);
        }
    }
}
