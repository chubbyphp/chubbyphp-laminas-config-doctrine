<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Persistence\Mapping\Driver\FileLocator;
use Doctrine\Persistence\Mapping\Driver\PHPDriver;
use Psr\Container\ContainerInterface;

final class PHPDriverFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): PHPDriver
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $driver */
        $driver = $doctrine['driver'] ?? [];

        /** @var array<string, mixed> $phpDriver */
        $phpDriver = $driver['phpDriver'] ?? [];

        $config = $this->resolveConfig($phpDriver);

        /** @var FileLocator|list<string>|string $locator */
        $locator = $this->resolveValue($container, $config['locator'] ?? []);

        unset($config['locator']);

        return new PHPDriver($locator);
    }
}
