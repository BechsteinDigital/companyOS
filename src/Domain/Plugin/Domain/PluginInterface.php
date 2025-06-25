<?php

namespace CompanyOS\Domain\Plugin\Domain;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

interface PluginInterface extends BundleInterface
{
    /**
     * Get plugin name
     */
    public function getPluginName(): string;

    /**
     * Get plugin version
     */
    public function getVersion(): string;

    /**
     * Get plugin author
     */
    public function getAuthor(): string;

    /**
     * Get plugin description
     */
    public function getDescription(): string;

    /**
     * Get plugin homepage URL
     */
    public function getHomepage(): ?string;

    /**
     * Get plugin license
     */
    public function getLicense(): ?string;

    /**
     * Get plugin icon path
     */
    public function getIcon(): ?string;

    /**
     * Get plugin label for admin interface
     */
    public function getLabel(): string;

    /**
     * Get plugin manufacturer
     */
    public function getManufacturer(): string;

    /**
     * Get plugin manufacturer URL
     */
    public function getManufacturerUrl(): ?string;

    /**
     * Get plugin support URL
     */
    public function getSupportUrl(): ?string;

    /**
     * Get plugin changelog
     */
    public function getChangelog(): ?array;

    /**
     * Get plugin requirements
     */
    public function getRequirements(): array;

    /**
     * Check if plugin is compatible with current system
     */
    public function isCompatible(): bool;

    /**
     * Install plugin
     */
    public function install(): void;

    /**
     * Uninstall plugin
     */
    public function uninstall(): void;

    /**
     * Activate plugin
     */
    public function activate(): void;

    /**
     * Deactivate plugin
     */
    public function deactivate(): void;

    /**
     * Update plugin
     */
    public function update(string $oldVersion): void;

    /**
     * Build plugin container
     */
    public function build(ContainerBuilder $container): void;

    /**
     * Get plugin configuration
     */
    public function getConfiguration(): array;

    /**
     * Get plugin routes
     */
    public function getRoutes(): array;

    /**
     * Get plugin services
     */
    public function getServices(): array;

    /**
     * Get plugin assets
     */
    public function getAssets(): array;
} 