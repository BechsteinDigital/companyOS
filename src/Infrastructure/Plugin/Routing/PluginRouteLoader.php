<?php

namespace CompanyOS\Infrastructure\Plugin\Routing;

use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class PluginRouteLoader extends Loader
{
    private bool $isLoaded = false;

    public function __construct(
        private PluginManager $pluginManager
    ) {
        parent::__construct();
    }

    public function load($resource, string $type = null): RouteCollection
    {
        if ($this->isLoaded) {
            throw new \RuntimeException('Plugin routes are already loaded');
        }

        $routeCollection = new RouteCollection();
        $loadedPlugins = $this->pluginManager->getLoadedPlugins();

        foreach ($loadedPlugins as $plugin) {
            $routesPath = $plugin->getPath() . '/Resources/config/routes.yaml';
            
            if (file_exists($routesPath)) {
                $loader = new YamlFileLoader(new FileLocator());
                $pluginRoutes = $loader->load($routesPath);
                
                // Add plugin prefix to routes
                foreach ($pluginRoutes->all() as $name => $route) {
                    $route->setPath('/plugins/' . $plugin->getPluginName() . $route->getPath());
                    $routeCollection->add($plugin->getPluginName() . '_' . $name, $route);
                }
            }
        }

        $this->isLoaded = true;
        return $routeCollection;
    }

    public function supports($resource, string $type = null): bool
    {
        return $type === 'plugin';
    }
} 