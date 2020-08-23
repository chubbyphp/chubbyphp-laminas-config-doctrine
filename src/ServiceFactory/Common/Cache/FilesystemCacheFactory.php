<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\Cache\FilesystemCache;
use Psr\Container\ContainerInterface;

final class FilesystemCacheFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): FilesystemCache
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['cache']['filesystem'] ?? []);

        $directory = $this->resolveValue($container, $config['directory'] ?? sys_get_temp_dir());
        $extension = $this->resolveValue($container, $config['extension'] ?? FilesystemCache::EXTENSION);
        $umask = $this->resolveValue($container, $config['umask'] ?? 0002);

        unset($config['directory'], $config['extension'], $config['umask']);

        $cache = new FilesystemCache($directory, $extension, $umask);

        $this->callSetters($container, $cache, $config);

        return $cache;
    }
}
