<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

final class RedisAdapterFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): RedisAdapter
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['redis'] ?? []);

        /** @var \Redis $redis */
        $redis = $this->resolveValue($container, $config['redis'] ?? null);

        $namespace = $config['namespace'] ?? '';
        $defaultLifetime = $config['defaultLifetime'] ?? 0;

        /** @var null|MarshallerInterface $marshaller */
        $marshaller = $this->resolveValue($container, $config['marshaller'] ?? null);

        return new RedisAdapter($redis, $namespace, $defaultLifetime, $marshaller);
    }
}
