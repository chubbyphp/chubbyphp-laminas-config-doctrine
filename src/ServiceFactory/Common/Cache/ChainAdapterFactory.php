<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ChainAdapter;

final class ChainAdapterFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ChainAdapter
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['chain'] ?? []);

        /** @var CacheItemPoolInterface[] $adapters */
        $adapters = $this->resolveValue($container, $config['adapters'] ?? []);

        $defaultLifetime = $config['defaultLifetime'] ?? 0;

        return new ChainAdapter($adapters, $defaultLifetime);
    }
}
