<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ClearCache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\ORM\Tools\Console\Command\ClearCache\EntityRegionCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

final class EntityRegionCommandFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): EntityRegionCommand
    {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $this->resolveDependency(
            $container,
            EntityManagerProvider::class,
            EntityManagerProviderFactory::class
        );

        return new EntityRegionCommand($entityManagerProvider);
    }
}
