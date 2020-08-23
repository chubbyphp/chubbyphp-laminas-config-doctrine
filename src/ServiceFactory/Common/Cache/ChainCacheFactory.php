<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\Cache\ChainCache;
use Psr\Container\ContainerInterface;

final class ChainCacheFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ChainCache
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['chain'] ?? []);

        $cacheProviders = $this->resolveValue($container, $config['cacheProviders'] ?? []);

        unset($config['cacheProviders']);

        $cache = new ChainCache($cacheProviders);

        $this->callSetters($container, $cache, $config);

        return $cache;
    }
}
