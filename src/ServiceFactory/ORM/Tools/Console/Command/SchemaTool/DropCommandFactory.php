<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console\Command\SchemaTool;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

final class DropCommandFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DropCommand
    {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $this->resolveDependency(
            $container,
            EntityManagerProvider::class,
            EntityManagerProviderFactory::class
        );

        return new DropCommand($entityManagerProvider);
    }
}
