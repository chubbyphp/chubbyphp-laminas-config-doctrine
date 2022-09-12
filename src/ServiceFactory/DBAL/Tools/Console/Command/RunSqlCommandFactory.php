<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\ContainerConnectionProviderFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Psr\Container\ContainerInterface;

final class RunSqlCommandFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): RunSqlCommand
    {
        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $this->resolveDependency($container, ConnectionProvider::class, ContainerConnectionProviderFactory::class);

        return new RunSqlCommand($connectionProvider);
    }
}
