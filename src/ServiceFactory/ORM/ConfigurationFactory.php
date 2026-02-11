<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Psr\Container\ContainerInterface;

final class ConfigurationFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): Configuration
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $orm */
        $orm = $doctrine['orm'] ?? [];

        /** @var array<string, mixed> $configurationConfig */
        $configurationConfig = $orm['configuration'] ?? [];

        $config = $this->resolveConfig($configurationConfig);

        /** @var list<array{name: string, className: class-string<SQLFilter>}> $filters */
        $filters = $this->resolveValue($container, $config['filters'] ?? []);

        unset($config['filters']);

        $configuration = new Configuration();

        $this->callAdders($configuration, $filters);
        $this->callSetters($container, $configuration, $config);

        return $configuration;
    }

    /**
     * @param list<array{name: string, className: class-string<SQLFilter>}> $filters
     */
    private function callAdders(Configuration $configuration, array $filters): void
    {
        foreach ($filters as $filter) {
            $configuration->addFilter($filter['name'], $filter['className']);
        }
    }
}
