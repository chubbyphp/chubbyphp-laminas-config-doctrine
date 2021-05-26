<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider as DoctrineEntityManagerProvider;
use Psr\Container\ContainerInterface;

final class EntityManagerProvider implements DoctrineEntityManagerProvider
{
    private ContainerInterface $container;

    public function getDefaultManager(): EntityManagerInterface
    {
        return $this->container->get(EntityManager::class);
    }

    public function getManager(string $name): EntityManagerInterface
    {
        return $this->container->get(EntityManager::class.$name);
    }
}
