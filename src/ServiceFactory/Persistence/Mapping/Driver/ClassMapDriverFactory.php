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
        $config = $this->resolveConfig($container->get('config')['doctrine']['driver']['classMap'] ?? []);

        $map = $this->resolveValue($container, $config['map'] ?? []);

        unset($config['map']);

        return new ClassMapDriver($map);
    }
}
