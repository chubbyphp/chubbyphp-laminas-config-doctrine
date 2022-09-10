<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\ContainerConnectionProviderFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Psr\Container\ContainerInterface;

final class DropCommandFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DropCommand
    {
        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $this->resolveDependency($container, ConnectionProvider::class, ContainerConnectionProviderFactory::class);

        return new DropCommand($connectionProvider);
    }
}
