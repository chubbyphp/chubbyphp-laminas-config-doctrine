<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\ConnectionCommand;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand;
use Psr\Container\ContainerInterface;

final class CreateCommandFactory
{
    public function __invoke(ContainerInterface $container): ConnectionCommand
    {
        return new ConnectionCommand(new CreateCommand(), $container);
    }
}
