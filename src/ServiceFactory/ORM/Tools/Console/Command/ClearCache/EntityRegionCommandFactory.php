<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ClearCache;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\EntityRegionCommand;
use Psr\Container\ContainerInterface;

final class EntityRegionCommandFactory
{
    public function __invoke(ContainerInterface $container): EntityManagerCommand
    {
        return new EntityManagerCommand(new EntityRegionCommand(), $container);
    }
}
