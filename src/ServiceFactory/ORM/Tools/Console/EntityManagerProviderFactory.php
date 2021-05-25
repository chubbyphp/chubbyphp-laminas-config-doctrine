<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

final class EntityManagerProviderFactory
{
    public function __invoke(ContainerInterface $container): EntityManagerProvider
    {
        return new EntityManagerProvider($container);
    }
}
