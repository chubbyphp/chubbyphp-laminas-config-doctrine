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

        /** @var array<string, mixed> $filters */
        $filters = $this->resolveValue($container, $config['filters'] ?? []);

        unset($config['filters']);

        $configuration = new Configuration();

        $this->callAdders($configuration, $filters);
        $this->callSetters($container, $configuration, $config);

        return $configuration;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function callAdders(Configuration $configuration, array $filters): void
    {
        foreach ($filters as $filter) {
            $configuration->addFilter($filter['name'], $filter['className']);
        }
    }
}
