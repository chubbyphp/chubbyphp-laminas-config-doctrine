<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

final class ApcuAdapterFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ApcuAdapter
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $cache */
        $cache = $doctrine['cache'] ?? [];

        /** @var array<string, mixed> $apcu */
        $apcu = $cache['apcu'] ?? [];

        $config = $this->resolveConfig($apcu);

        /** @var string $namespace */
        $namespace = $config['namespace'] ?? '';

        /** @var int $defaultLifetime */
        $defaultLifetime = $config['defaultLifetime'] ?? 0;

        /** @var null|string $version */
        $version = $config['version'] ?? null;

        /** @var null|MarshallerInterface $marshaller */
        $marshaller = $this->resolveValue($container, $config['marshaller'] ?? null);

        return new ApcuAdapter($namespace, $defaultLifetime, $version, $marshaller);
    }
}
