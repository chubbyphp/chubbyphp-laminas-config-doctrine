<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

final class ContainerEntityManagerProvider implements EntityManagerProvider
{
    public function __construct(private readonly ContainerInterface $container) {}

    public function getDefaultManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface */
        return $this->container->get(EntityManagerInterface::class);
    }

    public function getManager(string $name): EntityManagerInterface
    {
        /** @var EntityManagerInterface */
        return $this->container->get(EntityManagerInterface::class.$name);
    }
}
