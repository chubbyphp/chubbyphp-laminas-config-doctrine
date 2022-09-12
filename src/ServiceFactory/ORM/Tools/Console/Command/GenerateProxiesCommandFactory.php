<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\ContainerEntityManagerProviderFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

final class GenerateProxiesCommandFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): GenerateProxiesCommand
    {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $this->resolveDependency($container, EntityManagerProvider::class, ContainerEntityManagerProviderFactory::class);

        return new GenerateProxiesCommand($entityManagerProvider);
    }
}
