<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\Cache\ApcuCache;
use Psr\Container\ContainerInterface;

final class ApcuCacheFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ApcuCache
    {
        $cache = new ApcuCache();

        $this->callSetters(
            $container,
            $cache,
            $this->resolveConfig($container->get('config')['doctrine']['cache']['apcu'] ?? [])
        );

        return $cache;
    }
}
