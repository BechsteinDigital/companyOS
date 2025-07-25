<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\GetPluginDependenciesQuery;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\DTO\PluginDependencyResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;

final class GetPluginDependenciesQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly PluginRepositoryInterface $pluginRepository
    ) {
    }

    public function __invoke(GetPluginDependenciesQuery $query): array
    {
        $plugin = $this->pluginRepository->findById($query->getPluginId());
        
        if (!$plugin) {
            throw new \InvalidArgumentException('Plugin not found');
        }

        $dependencies = $plugin->getDependencies();
        $dependencyPlugins = [];

        foreach ($dependencies as $dependency) {
            $dependencyPlugin = $this->pluginRepository->findByName($dependency['name']);
            
            if ($dependencyPlugin) {
                $dependencyPlugins[] = new PluginDependencyResponse(
                    name: $dependency['name'],
                    version: $dependency['version'],
                    isInstalled: true,
                    isActive: $dependencyPlugin->isActive(),
                    currentVersion: $dependencyPlugin->getVersion()->value()
                );
            } else {
                $dependencyPlugins[] = new PluginDependencyResponse(
                    name: $dependency['name'],
                    version: $dependency['version'],
                    isInstalled: false,
                    isActive: false,
                    currentVersion: null
                );
            }
        }

        return $dependencyPlugins;
    }
} 