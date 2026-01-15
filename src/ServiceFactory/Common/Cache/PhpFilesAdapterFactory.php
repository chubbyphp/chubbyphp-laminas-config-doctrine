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
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $cache */
        $cache = $doctrine['cache'] ?? [];

        /** @var array<string, mixed> $phpfiles */
        $phpfiles = $cache['phpfiles'] ?? [];

        $config = $this->resolveConfig($phpfiles);

        /** @var string $namespace */
        $namespace = $config['namespace'] ?? '';

        /** @var int $defaultLifetime */
        $defaultLifetime = $config['defaultLifetime'] ?? 0;

        /** @var null|string $directory */
        $directory = $config['directory'] ?? null;

        /** @var bool $appendOnly */
        $appendOnly = $config['appendOnly'] ?? false;

        return new PhpFilesAdapter($namespace, $defaultLifetime, $directory, $appendOnly);
    }
}
