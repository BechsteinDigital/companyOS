<?php

namespace CompanyOS\Domain\Plugin\Application\CommandHandler;

use CompanyOS\Domain\Plugin\Application\Command\UpdatePluginCommand;
use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
use CompanyOS\Domain\Plugin\Domain\Event\PluginUpdated;
use CompanyOS\Application\Command\CommandHandlerInterface;
use CompanyOS\Domain\Shared\ValueObject\Uuid;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class UpdatePluginCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private PluginManager $pluginManager,
        private DomainEventDispatcher $eventDispatcher,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(UpdatePluginCommand $command): void
    {
        $pluginId = Uuid::fromString($command->pluginId);
        
        // Get current plugin state for rollback
        $currentPlugin = $this->pluginManager->getPluginById($pluginId);
        if (!$currentPlugin) {
            throw new \InvalidArgumentException('Plugin not found');
        }

        $backupPath = null;
        $wasActive = $currentPlugin->isActive();

        try {
            // Create backup
            $backupPath = $this->createBackup($currentPlugin->getName());
            
            // Deactivate plugin before update
            if ($wasActive) {
                $this->pluginManager->deactivatePlugin($pluginId);
            }

            // Extract and install new version
            $this->extractUpdate($command->updateFilePath, $currentPlugin->getName());
            
            // Run migrations
            if (!$this->pluginManager->runPluginMigrations($currentPlugin->getName())) {
                throw new \RuntimeException('Plugin migration failed');
            }

            // Reactivate if it was active before
            if ($wasActive) {
                $this->pluginManager->activatePlugin($pluginId);
            }

            // Update plugin entity
            $this->pluginManager->updatePluginVersion($pluginId, $command->newVersion);

            // Dispatch domain event
            $event = new PluginUpdated(
                $pluginId,
                $currentPlugin->getName(),
                $currentPlugin->getVersion(),
                $command->newVersion,
                $command->changelog
            );
            $this->eventDispatcher->dispatch($event);

            $this->logger->info("Plugin {$currentPlugin->getName()} updated successfully to version {$command->newVersion}");

        } catch (\Exception $e) {
            // Rollback on error
            $this->rollback($currentPlugin->getName(), $backupPath, $wasActive, $pluginId);
            throw $e;
        } finally {
            // Cleanup backup
            if ($backupPath && file_exists($backupPath)) {
                unlink($backupPath);
            }
        }
    }

    private function createBackup(string $pluginName): string
    {
        $pluginPath = "custom/plugins/{$pluginName}";
        $backupPath = "var/backups/plugins/{$pluginName}_" . date('Y-m-d_H-i-s') . ".tar.gz";
        
        if (!is_dir(dirname($backupPath))) {
            mkdir(dirname($backupPath), 0755, true);
        }

        $command = "tar -czf {$backupPath} -C " . dirname($pluginPath) . " " . basename($pluginPath);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException("Failed to create backup for plugin {$pluginName}");
        }

        return $backupPath;
    }

    private function extractUpdate(string $updateFilePath, string $pluginName): void
    {
        $pluginPath = "custom/plugins/{$pluginName}";
        
        // Remove current version
        if (is_dir($pluginPath)) {
            $this->removeDirectory($pluginPath);
        }

        // Extract new version
        $command = "tar -xzf {$updateFilePath} -C custom/plugins/";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException("Failed to extract plugin update");
        }
    }

    private function rollback(string $pluginName, ?string $backupPath, bool $wasActive, Uuid $pluginId): void
    {
        $this->logger->warning("Rolling back plugin {$pluginName} due to update failure");

        if ($backupPath && file_exists($backupPath)) {
            $pluginPath = "custom/plugins/{$pluginName}";
            
            // Remove failed update
            if (is_dir($pluginPath)) {
                $this->removeDirectory($pluginPath);
            }

            // Restore backup
            $command = "tar -xzf {$backupPath} -C custom/plugins/";
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && $wasActive) {
                $this->pluginManager->activatePlugin($pluginId);
            }
        }
    }

    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $path . '/' . $file;
            
            if (is_dir($filePath)) {
                $this->removeDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        
        rmdir($path);
    }
} 