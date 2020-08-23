<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\EventManager;
use Psr\Container\ContainerInterface;

final class EventManagerFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): EventManager
    {
        $config = $this->resolveConfig($container->get('config')['doctrine']['eventManager'] ?? []);

        $eventManager = new EventManager();

        foreach ($this->resolveValue($container, $config['listeners'] ?? []) as $listener) {
            $eventManager->addEventListener($listener['events'], $listener['listener']);
        }

        foreach ($this->resolveValue($container, $config['subscribers'] ?? []) as $subscriber) {
            $eventManager->addEventSubscriber($subscriber);
        }

        return $eventManager;
    }
}
