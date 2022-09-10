<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

final class FilesystemAdapterFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): FilesystemAdapter
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['filesystem'] ?? []);

        $namespace = $config['namespace'] ?? '';
        $defaultLifetime = $config['defaultLifetime'] ?? 0;
        $directory = $config['directory'] ?? null;

        /** @var null|MarshallerInterface $marshaller */
        $marshaller = $this->resolveValue($container, $config['marshaller'] ?? null);

        return new FilesystemAdapter($namespace, $defaultLifetime, $directory, $marshaller);
    }
}
