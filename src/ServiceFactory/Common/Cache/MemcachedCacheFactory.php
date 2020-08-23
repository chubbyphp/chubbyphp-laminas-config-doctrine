<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\Cache\MemcachedCache;
use Psr\Container\ContainerInterface;

final class MemcachedCacheFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): MemcachedCache
    {
        $cache = new MemcachedCache();

        $this->callSetters(
            $container,
            $cache,
            $this->resolveConfig($container->get('config')['doctrine']['cache']['memcached'] ?? [])
        );

        return $cache;
    }
}
