<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Plugin\Application\Service;

use CompanyOS\Domain\Plugin\Application\Command\ActivatePluginCommand;
use CompanyOS\Domain\Plugin\Application\Command\DeactivatePluginCommand;
use CompanyOS\Domain\Plugin\Application\Command\DeletePluginCommand;
use CompanyOS\Domain\Plugin\Application\Command\InstallPluginCommand;
use CompanyOS\Domain\Plugin\Application\Command\UpdatePluginCommand;
use CompanyOS\Domain\Plugin\Application\DTO\InstallPluginRequest;
use CompanyOS\Domain\Plugin\Application\DTO\UpdatePluginRequest;
use CompanyOS\Domain\Plugin\Application\Query\CheckPluginCompatibilityQuery;
use CompanyOS\Domain\Plugin\Application\Query\GetActivePluginsQuery;
use CompanyOS\Domain\Plugin\Application\Query\GetAllPluginsQuery;
use CompanyOS\Domain\Plugin\Application\Query\GetPluginDependenciesQuery;
use CompanyOS\Domain\Plugin\Application\Query\GetPluginQuery;
use CompanyOS\Application\Command\CommandBusInterface;
use CompanyOS\Application\Query\QueryBusInterface;
use CompanyOS\Domain\ValueObject\Uuid;

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
        $command = new ActivatePluginCommand(Uuid::fromString($pluginId));
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