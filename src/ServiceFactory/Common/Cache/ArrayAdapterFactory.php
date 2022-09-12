<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class ArrayAdapterFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ArrayAdapter
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['array'] ?? []);

        $defaultLifetime = $config['defaultLifetime'] ?? 0;
        $storeSerialized = $config['storeSerialized'] ?? true;
        $maxLifetime = $config['maxLifetime'] ?? 0;
        $maxItems = $config['maxItems'] ?? 0;

        return new ArrayAdapter($defaultLifetime, $storeSerialized, $maxLifetime, $maxItems);
    }
}
