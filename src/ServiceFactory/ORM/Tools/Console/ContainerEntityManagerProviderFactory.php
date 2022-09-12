<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM\Tools\Console;

use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\ContainerEntityManagerProvider;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

final class ContainerEntityManagerProviderFactory
{
    public function __invoke(ContainerInterface $container): EntityManagerProvider
    {
        return new ContainerEntityManagerProvider($container);
    }
}
