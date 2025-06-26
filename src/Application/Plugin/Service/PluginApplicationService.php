<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Service;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\ActivatePluginCommand as PluginActivatePluginCommand;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\DeactivatePluginCommand;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\DeletePluginCommand;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\InstallPluginCommand;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\UpdatePluginCommand;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\DTO\InstallPluginRequest;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\DTO\UpdatePluginRequest;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\CheckPluginCompatibilityQuery;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\GetActivePluginsQuery;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\GetAllPluginsQuery;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\GetPluginDependenciesQuery;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\GetPluginQuery;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandBusInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryBusInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

final class PluginApplicationService
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus
    ) {
    }

    public function installPlugin(InstallPluginRequest $request): void
    {
        $command = new InstallPluginCommand(
            name: $request->name,
            version: $request->version,
            source: $request->source,
            dependencies: $request->dependencies,
            settings: $request->settings
        );

        $this->commandBus->dispatch($command);
    }

    public function updatePlugin(UpdatePluginRequest $request): void
    {
        $command = new UpdatePluginCommand(
            pluginId: Uuid::fromString($request->pluginId),
            version: $request->version,
            changelog: $request->changelog,
            dependencies: $request->dependencies
        );

        $this->commandBus->dispatch($command);
    }

    public function activatePlugin(string $pluginId): void
    {
        $command = new PluginActivatePluginCommand(Uuid::fromString($pluginId));
        $this->commandBus->dispatch($command);
    }

    public function deactivatePlugin(string $pluginId): void
    {
        $command = new DeactivatePluginCommand(Uuid::fromString($pluginId));
        $this->commandBus->dispatch($command);
    }

    public function deletePlugin(string $pluginId): void
    {
        $command = new DeletePluginCommand(Uuid::fromString($pluginId));
        $this->commandBus->dispatch($command);
    }

    public function getAllPlugins(): array
    {
        $query = new GetAllPluginsQuery();
        return $this->queryBus->ask($query);
    }

    public function getPlugin(string $pluginId): mixed
    {
        $query = new GetPluginQuery(Uuid::fromString($pluginId));
        return $this->queryBus->ask($query);
    }

    public function getActivePlugins(?string $category = null): array
    {
        $query = new GetActivePluginsQuery($category);
        return $this->queryBus->ask($query);
    }

    public function getPluginDependencies(string $pluginId): array
    {
        $query = new GetPluginDependenciesQuery(Uuid::fromString($pluginId));
        return $this->queryBus->ask($query);
    }

    public function checkPluginCompatibility(
        string $pluginName,
        string $version,
        array $systemRequirements
    ): mixed {
        $query = new CheckPluginCompatibilityQuery($pluginName, $version, $systemRequirements);
        return $this->queryBus->ask($query);
    }
} 