<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

final class MemcachedAdapterFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): MemcachedAdapter
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $cache */
        $cache = $doctrine['cache'] ?? [];

        /** @var array<string, mixed> $memcached */
        $memcached = $cache['memcached'] ?? [];

        $config = $this->resolveConfig($memcached);

        /** @var \Memcached $client */
        $client = $this->resolveValue($container, $config['client'] ?? null);

        /** @var string $namespace */
        $namespace = $config['namespace'] ?? '';

        /** @var int $defaultLifetime */
        $defaultLifetime = $config['defaultLifetime'] ?? 0;

        /** @var null|MarshallerInterface $marshaller */
        $marshaller = $this->resolveValue($container, $config['marshaller'] ?? null);

        return new MemcachedAdapter($client, $namespace, $defaultLifetime, $marshaller);
    }
}
