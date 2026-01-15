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
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $cache */
        $cache = $doctrine['cache'] ?? [];

        /** @var array<string, mixed> $chain */
        $chain = $cache['chain'] ?? [];

        $config = $this->resolveConfig($chain);

        /** @var CacheItemPoolInterface[] $adapters */
        $adapters = $this->resolveValue($container, $config['adapters'] ?? []);

        /** @var int $defaultLifetime */
        $defaultLifetime = $config['defaultLifetime'] ?? 0;

        return new ChainAdapter($adapters, $defaultLifetime);
    }
}
