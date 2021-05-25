<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\EntityManagerProviderFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

final class ConvertMappingCommandFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): ConvertMappingCommand
    {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $this->resolveDependency(
            $container,
            EntityManagerProvider::class,
            EntityManagerProviderFactory::class
        );

        return new ConvertMappingCommand($entityManagerProvider);
    }
}
