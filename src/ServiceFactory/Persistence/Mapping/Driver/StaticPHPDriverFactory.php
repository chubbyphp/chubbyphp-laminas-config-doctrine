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
        $config = $this->resolveConfig($container->get('config')['doctrine']['driver']['staticPhpDriver'] ?? []);

        $paths = $this->resolveValue($container, $config['paths'] ?? []);

        unset($config['paths']);

        return new StaticPHPDriver($paths);
    }
}
