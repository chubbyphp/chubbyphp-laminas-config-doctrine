<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\Tools\Console\Command\Database;

use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\ConnectionCommand;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand;
use Psr\Container\ContainerInterface;

final class DropCommandFactory
{
    public function __invoke(ContainerInterface $container): ConnectionCommand
    {
        return new ConnectionCommand(new DropCommand(), $container);
    }
}
