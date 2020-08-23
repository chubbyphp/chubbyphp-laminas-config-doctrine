<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\Cache\RedisCache;
use Psr\Container\ContainerInterface;

final class RedisCacheFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): RedisCache
    {
        $cache = new RedisCache();

        $this->callSetters(
            $container,
            $cache,
            $this->resolveConfig($container->get('config')['doctrine']['cache']['redis'] ?? [])
        );

        return $cache;
    }
}
