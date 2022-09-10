<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\ContainerConnectionProvider;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Psr\Container\ContainerInterface;

final class ContainerConnectionProviderFactory
{
    public function __invoke(ContainerInterface $container): ConnectionProvider
    {
        return new ContainerConnectionProvider($container);
    }
}
