<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ClearCache;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\CollectionRegionCommand;
use Psr\Container\ContainerInterface;

final class CollectionRegionCommandFactory
{
    public function __invoke(ContainerInterface $container): EntityManagerCommand
    {
        return new EntityManagerCommand(new CollectionRegionCommand(), $container);
    }
}
