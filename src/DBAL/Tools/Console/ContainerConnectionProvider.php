<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Psr\Container\ContainerInterface;

final class ContainerConnectionProvider implements ConnectionProvider
{
    public function __construct(private ContainerInterface $container) {}

    public function getDefaultConnection(): Connection
    {
        return $this->container->get(Connection::class);
    }

    public function getConnection(string $name): Connection
    {
        return $this->container->get(Connection::class.$name);
    }
}
