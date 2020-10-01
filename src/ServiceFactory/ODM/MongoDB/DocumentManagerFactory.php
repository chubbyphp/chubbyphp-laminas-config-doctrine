<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\ODM\MongoDB;

use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\EventManagerFactory;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\MongoDB\ClientFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

final class DocumentManagerFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DocumentManager
    {
        /** @var Client $client */
        $client = $this->resolveDependency($container, Client::class, ClientFactory::class);

        /** @var Configuration $configuration */
        $configuration = $this->resolveDependency($container, Configuration::class, ConfigurationFactory::class);

        /** @var EventManager $eventManager */
        $eventManager = $this->resolveDependency($container, EventManager::class, EventManagerFactory::class);

        return DocumentManager::create($client, $configuration, $eventManager);
    }
}
