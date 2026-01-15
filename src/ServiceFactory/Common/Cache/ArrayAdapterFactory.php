<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class ArrayAdapterFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ArrayAdapter
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $cache */
        $cache = $doctrine['cache'] ?? [];

        /** @var array<string, mixed> $array */
        $array = $cache['array'] ?? [];

        $config = $this->resolveConfig($array);

        /** @var int $defaultLifetime */
        $defaultLifetime = $config['defaultLifetime'] ?? 0;

        /** @var bool $storeSerialized */
        $storeSerialized = $config['storeSerialized'] ?? true;

        /** @var float $maxLifetime */
        $maxLifetime = $config['maxLifetime'] ?? 0;

        /** @var int $maxItems */
        $maxItems = $config['maxItems'] ?? 0;

        return new ArrayAdapter($defaultLifetime, $storeSerialized, $maxLifetime, $maxItems);
    }
}
