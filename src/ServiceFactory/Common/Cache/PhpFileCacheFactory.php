<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\Cache\PhpFileCache;
use Psr\Container\ContainerInterface;

final class PhpFileCacheFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): PhpFileCache
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['phpFile'] ?? []);

        $directory = $this->resolveValue($container, $config['directory'] ?? sys_get_temp_dir());
        $extension = $this->resolveValue($container, $config['extension'] ?? PhpFileCache::EXTENSION);
        $umask = $this->resolveValue($container, $config['umask'] ?? 0002);

        unset($config['directory'], $config['extension'], $config['umask']);

        $cache = new PhpFileCache($directory, $extension, $umask);

        $this->callSetters($container, $cache, $config);

        return $cache;
    }
}
