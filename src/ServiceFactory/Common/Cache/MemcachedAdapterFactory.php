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
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['memcached'] ?? []);

        /** @var \Memcached $client */
        $client = $this->resolveValue($container, $config['client'] ?? null);

        $namespace = $config['namespace'] ?? '';
        $defaultLifetime = $config['defaultLifetime'] ?? 0;

        /** @var null|MarshallerInterface $marshaller */
        $marshaller = $this->resolveValue($container, $config['marshaller'] ?? null);

        return new MemcachedAdapter($client, $namespace, $defaultLifetime, $marshaller);
    }
}
