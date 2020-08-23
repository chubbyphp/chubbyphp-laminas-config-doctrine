<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Persistence\Mapping\Driver;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Persistence\Mapping\Driver\PHPDriver;
use Psr\Container\ContainerInterface;

final class PHPDriverFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): PHPDriver
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['driver']['phpDriver'] ?? []);

        $locator = $this->resolveValue($container, $config['locator'] ?? []);

        unset($config['locator']);

        return new PHPDriver($locator);
    }
}
