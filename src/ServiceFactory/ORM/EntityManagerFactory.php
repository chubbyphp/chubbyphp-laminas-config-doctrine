<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ORM;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\EventManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\DBAL\ConnectionFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

final class EntityManagerFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): EntityManager
    {
        /** @var Connection $connection */
        $connection = $this->resolveDependency($container, Connection::class, ConnectionFactory::class);

        /** @var Configuration $configuration */
        $configuration = $this->resolveDependency($container, Configuration::class, ConfigurationFactory::class);

        /** @var EventManager $eventManager */
        $eventManager = $this->resolveDependency($container, EventManager::class, EventManagerFactory::class);

        return new EntityManager($connection, $configuration, $eventManager);
    }
}
