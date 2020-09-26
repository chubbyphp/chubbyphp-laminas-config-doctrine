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
        $namedQueries = $this->resolveValue($container, $config['namedQueries'] ?? []);
        $namedNativeQueries = $this->resolveValue($container, $config['namedNativeQueries'] ?? []);
        $filters = $this->resolveValue($container, $config['filters'] ?? []);

        unset($config['namedQueries'], $config['namedNativeQueries'], $config['filters']);

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
