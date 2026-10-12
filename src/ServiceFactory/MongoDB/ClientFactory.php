<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\MongoDB;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-type UriOptions array{
 *     readPreference?: string,
 *     readConcernLevel?: string,
 *     w?: int|string,
 *     wtimeoutMS?: int,
 *     journal?: bool,
 *     retryWrites?: bool,
 *     retryReads?: bool,
 *     ssl?: bool,
 *     tls?: bool,
 *     authSource?: string,
 *     authMechanism?: string,
 *     replicaSet?: string,
 *     appname?: string,
 *     loadBalanced?: bool,
 *     directConnection?: bool,
 *     serverSelectionTimeoutMS?: int,
 *     maxPoolSize?: int,
 *     minPoolSize?: int,
 * }
 * @phpstan-type DriverOptions array{
 *     typeMap?: array<string, mixed>,
 *     driver?: array{name?: string, version?: string, platform?: string},
 * }
 */
final class ClientFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): Client
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $mongodb */
        $mongodb = $doctrine['mongodb'] ?? [];

        /** @var array<string, mixed> $clientConfig */
        $clientConfig = $mongodb['client'] ?? [];

        /** @var array<string, mixed> $config */
        $config = $this->resolveConfig($clientConfig);

        /** @var null|string $uri */
        $uri = $this->resolveValue($container, $config['uri'] ?? 'mongodb://127.0.0.1/');

        /** @var UriOptions $uriOptions */
        $uriOptions = $this->resolveValue($container, $config['uriOptions'] ?? []);

        /** @var DriverOptions $driverOptions */
        $driverOptions = $this->resolveValue($container, $config['driverOptions'] ?? []);

        unset($config['uri'], $config['uriOptions'], $config['driverOptions']);

        return new Client($uri, $uriOptions, $driverOptions);
    }
}
