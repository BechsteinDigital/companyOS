<?php

namespace CompanyOS\Infrastructure\Plugin\EventSubscriber;

use CompanyOS\Infrastructure\Plugin\Routing\PluginRouteLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Psr\Log\LoggerInterface;

class PluginRouteSubscriber implements EventSubscriberInterface
{
    private bool $routesLoaded = false;

    public function __construct(
        private PluginRouteLoader $pluginRouteLoader,
        private RouterInterface $router,
        private LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1000], // High priority
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->routesLoaded || !$event->isMainRequest()) {
            return;
        }

        try {
            // Load plugin routes
            $pluginRoutes = $this->pluginRouteLoader->load('plugin', 'plugin');
            
            // Add routes to router
            $routeCollection = $this->router->getRouteCollection();
            $routeCollection->addCollection($pluginRoutes);
            
            $this->routesLoaded = true;
        } catch (\Exception $e) {
            // Log error but don't break the application
            $this->logger->error('Failed to load plugin routes: ' . $e->getMessage());
        }
    }
} 