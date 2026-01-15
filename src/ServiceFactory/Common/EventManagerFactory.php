<?php

declare(strict_types=1);

namespace Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Psr\Container\ContainerInterface;

final class EventManagerFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): EventManager
    {
        /** @var array<string, mixed> $containerConfig */
        $containerConfig = $container->get('config');

        /** @var array<string, mixed> $doctrine */
        $doctrine = $containerConfig['doctrine'] ?? [];

        /** @var array<string, mixed> $eventManagerConfig */
        $eventManagerConfig = $doctrine['eventManager'] ?? [];

        $config = $this->resolveConfig($eventManagerConfig);

        $eventManager = new EventManager();

        /** @var array<int, array{events: array<string>|string, listener: object}> $listeners */
        $listeners = $this->resolveValue($container, $config['listeners'] ?? []);
        foreach ($listeners as $listener) {
            $eventManager->addEventListener($listener['events'], $listener['listener']);
        }

        /** @var array<int, EventSubscriber> $subscribers */
        $subscribers = $this->resolveValue($container, $config['subscribers'] ?? []);
        foreach ($subscribers as $subscriber) {
            $eventManager->addEventSubscriber($subscriber);
        }

        return $eventManager;
    }
}
