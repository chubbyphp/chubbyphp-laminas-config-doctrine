<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\ClearCache;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\ORM\Tools\Console\Command\ClearCache\QueryRegionCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

final class QueryRegionCommandFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): QueryRegionCommand
    {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $this->resolveDependency(
            $container,
            EntityManagerProvider::class,
            EntityManagerProviderFactory::class
        );

        return new QueryRegionCommand($entityManagerProvider);
    }
}
