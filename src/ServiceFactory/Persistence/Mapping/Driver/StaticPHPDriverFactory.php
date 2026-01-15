<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Persistence\Mapping\Driver\StaticPHPDriver;
use Psr\Container\ContainerInterface;

final class StaticPHPDriverFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): StaticPHPDriver
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $driver */
        $driver = $doctrine['driver'] ?? [];

        /** @var array<string, mixed> $staticPhpDriver */
        $staticPhpDriver = $driver['staticPhpDriver'] ?? [];

        $config = $this->resolveConfig($staticPhpDriver);

        /** @var array<int, string>|string $paths */
        $paths = $this->resolveValue($container, $config['paths'] ?? []);

        unset($config['paths']);

        return new StaticPHPDriver($paths);
    }
}
