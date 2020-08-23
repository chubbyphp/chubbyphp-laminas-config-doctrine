<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\SchemaTool;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Psr\Container\ContainerInterface;

final class CreateCommandFactory
{
    public function __invoke(ContainerInterface $container): EntityManagerCommand
    {
        return new EntityManagerCommand(new CreateCommand(), $container);
    }
}
