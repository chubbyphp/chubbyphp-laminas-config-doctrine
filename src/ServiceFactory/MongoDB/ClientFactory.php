<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\MongoDB;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

final class ClientFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): Client
    {
        /** @var array<string, mixed> $config */
        $config = $this->resolveConfig($container->get('config')['doctrine']['mongodb']['client'] ?? []);

        $uri = $this->resolveValue($container, $config['uri'] ?? 'mongodb://127.0.0.1/');
        $uriOptions = $this->resolveValue($container, $config['uriOptions'] ?? []);
        $driverOptions = $this->resolveValue($container, $config['driverOptions'] ?? []);

        unset($config['uri'], $config['uriOptions'], $config['driverOptions']);

        return new Client($uri, $uriOptions, $driverOptions);
    }
}
