<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\Cache\ArrayCache;
use Psr\Container\ContainerInterface;

final class ArrayCacheFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ArrayCache
    {
        $cache = new ArrayCache();

        $this->callSetters(
            $container,
            $cache,
            $this->resolveConfig($container->get('config')['doctrine']['cache']['array'] ?? [])
        );

        return $cache;
    }
}
