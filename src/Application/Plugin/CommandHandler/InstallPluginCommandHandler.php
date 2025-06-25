<?php

namespace CompanyOS\Domain\Plugin\Application\CommandHandler;

use CompanyOS\Domain\Plugin\Application\Command\InstallPluginCommand;
use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
use CompanyOS\Domain\Plugin\Domain\Entity\Plugin;
use CompanyOS\Domain\Plugin\Domain\Event\PluginInstalled;
use CompanyOS\Application\Command\CommandHandlerInterface;
use CompanyOS\Domain\Shared\ValueObject\Uuid;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;

class InstallPluginCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private PluginManager $pluginManager,
        private DomainEventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(InstallPluginCommand $command): Plugin
    {
        // Check if plugin exists in filesystem
        $pluginDirectories = $this->pluginManager->getPluginDirectories();
        $directories = explode(',', $pluginDirectories);
        
        $pluginPath = null;
        foreach ($directories as $directory) {
            $path = trim($directory) . '/' . $command->name;
            if (is_dir($path)) {
                $pluginPath = $path;
                break;
            }
        }

        if (!$pluginPath) {
            throw new \InvalidArgumentException('Plugin not found in filesystem');
        }

        // Check if plugin is compatible
        if (!$this->pluginManager->validatePluginCompatibility($command->name)) {
            throw new \InvalidArgumentException('Plugin is not compatible with current system');
        }

        // Check dependencies
        $dependencies = $this->pluginManager->getPluginDependencies($command->name);
        if (!empty($dependencies)) {
            $this->validateDependencies($dependencies);
        }

        // Install plugin
        $plugin = $this->pluginManager->installPlugin(
            $command->name,
            $command->version,
            $command->author,
            $command->meta
        );

        // Run plugin migrations safely
        if (!$this->pluginManager->runPluginMigrations($command->name)) {
            throw new \RuntimeException('Failed to run plugin migrations');
        }

        // Dispatch domain event
        $event = new PluginInstalled(
            $plugin->getId(),
            $plugin->getName(),
            $plugin->getVersion(),
            $plugin->getAuthor()
        );

        $this->eventDispatcher->dispatch($event);

        return $plugin;
    }

    /**
     * Validate plugin dependencies
     */
    private function validateDependencies(array $dependencies): void
    {
        // Check PHP version
        if (isset($dependencies['php'])) {
            $requiredVersion = $dependencies['php'];
            if (!version_compare(PHP_VERSION, $requiredVersion, '>=')) {
                throw new \InvalidArgumentException("PHP version {$requiredVersion} required, but " . PHP_VERSION . " installed");
            }
        }

        // Check Symfony version
        if (isset($dependencies['symfony'])) {
            $requiredVersion = $dependencies['symfony'];
            if (!version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, $requiredVersion, '>=')) {
                throw new \InvalidArgumentException("Symfony version {$requiredVersion} required, but " . \Symfony\Component\HttpKernel\Kernel::VERSION . " installed");
            }
        }

        // Check other plugins
        if (isset($dependencies['plugins'])) {
            foreach ($dependencies['plugins'] as $pluginName => $version) {
                $plugin = $this->pluginManager->getPlugin($pluginName);
                if (!$plugin) {
                    throw new \InvalidArgumentException("Required plugin {$pluginName} not found");
                }
                
                if (!version_compare($plugin->getVersion(), $version, '>=')) {
                    throw new \InvalidArgumentException("Plugin {$pluginName} version {$version} required, but " . $plugin->getVersion() . " installed");
                }
            }
        }
    }
} 