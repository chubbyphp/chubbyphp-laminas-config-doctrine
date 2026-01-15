<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapDriver;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class ClassMapDriverFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ClassMapDriver
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $driver */
        $driver = $doctrine['driver'] ?? [];

        /** @var array<string, mixed> $classMap */
        $classMap = $driver['classMap'] ?? [];

        $config = $this->resolveConfig($classMap);

        /** @var array<string, string> $map */
        $map = $this->resolveValue($container, $config['map'] ?? []);

        unset($config['map']);

        return new ClassMapDriver($map);
    }
}
