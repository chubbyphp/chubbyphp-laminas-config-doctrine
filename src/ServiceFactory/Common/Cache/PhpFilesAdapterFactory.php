<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

final class PhpFilesAdapterFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): PhpFilesAdapter
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['phpfiles'] ?? []);

        $namespace = $config['namespace'] ?? '';
        $defaultLifetime = $config['defaultLifetime'] ?? 0;
        $directory = $config['directory'] ?? null;
        $appendOnly = $config['appendOnly'] ?? false;

        return new PhpFilesAdapter($namespace, $defaultLifetime, $directory, $appendOnly);
    }
}
