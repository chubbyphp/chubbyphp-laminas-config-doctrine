<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\ConnectionCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Psr\Container\ContainerInterface;

final class RunSqlCommandFactory
{
    public function __invoke(ContainerInterface $container): ConnectionCommand
    {
        return new ConnectionCommand(new RunSqlCommand(), $container);
    }
}
