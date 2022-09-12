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
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['apcu'] ?? []);

        $namespace = $config['namespace'] ?? '';
        $defaultLifetime = $config['defaultLifetime'] ?? 0;
        $version = $config['version'] ?? null;

        /** @var null|MarshallerInterface $marshaller */
        $marshaller = $this->resolveValue($container, $config['marshaller'] ?? null);

        return new ApcuAdapter($namespace, $defaultLifetime, $version, $marshaller);
    }
}
