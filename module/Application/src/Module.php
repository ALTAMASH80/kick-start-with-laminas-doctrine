<?php

declare(strict_types=1);

namespace Application;

use Laminas\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        $listener = $serviceManager->get(\LmcRbacMvc\View\Strategy\UnauthorizedStrategy::class);
        $listener->attach($event->getApplication()->getEventManager());
    }

    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }
}
