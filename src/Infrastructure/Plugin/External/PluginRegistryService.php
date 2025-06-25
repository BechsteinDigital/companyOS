<?php

declare(strict_types=1);

namespace CompanyOS\Infrastructure\Plugin\External;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class PluginRegistryService
{
    private const CACHE_KEY = 'plugin_registry';
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly Connection $connection,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger
    ) {
    }

    public function registerPlugin(
        string $pluginId,
        string $pluginName,
        string $version,
        string $author
    ): void {
        $sql = 'INSERT INTO plugin_registry (plugin_id, plugin_name, version, author, status, created_at) 
                VALUES (:pluginId, :pluginName, :version, :author, :status, :createdAt)';

        $this->connection->executeStatement($sql, [
            'pluginId' => $pluginId,
            'pluginName' => $pluginName,
            'version' => $version,
            'author' => $author,
            'status' => 'installed',
            'createdAt' => new \DateTimeImmutable()
        ]);

        $this->logger->info('Plugin registered in registry', [
            'pluginId' => $pluginId,
            'pluginName' => $pluginName,
            'version' => $version
        ]);
    }

    public function activatePlugin(string $pluginId): void
    {
        $sql = 'UPDATE plugin_registry SET status = :status, updated_at = :updatedAt WHERE plugin_id = :pluginId';

        $this->connection->executeStatement($sql, [
            'pluginId' => $pluginId,
            'status' => 'active',
            'updatedAt' => new \DateTimeImmutable()
        ]);

        $this->logger->info('Plugin activated in registry', ['pluginId' => $pluginId]);
    }

    public function deactivatePlugin(string $pluginId): void
    {
        $sql = 'UPDATE plugin_registry SET status = :status, updated_at = :updatedAt WHERE plugin_id = :pluginId';

        $this->connection->executeStatement($sql, [
            'pluginId' => $pluginId,
            'status' => 'inactive',
            'updatedAt' => new \DateTimeImmutable()
        ]);

        $this->logger->info('Plugin deactivated in registry', ['pluginId' => $pluginId]);
    }

    public function updatePlugin(string $pluginId, string $newVersion, array $changelog): void
    {
        $sql = 'UPDATE plugin_registry SET version = :version, changelog = :changelog, updated_at = :updatedAt 
                WHERE plugin_id = :pluginId';

        $this->connection->executeStatement($sql, [
            'pluginId' => $pluginId,
            'version' => $newVersion,
            'changelog' => json_encode($changelog),
            'updatedAt' => new \DateTimeImmutable()
        ]);

        $this->logger->info('Plugin updated in registry', [
            'pluginId' => $pluginId,
            'newVersion' => $newVersion
        ]);
    }

    public function unregisterPlugin(string $pluginId): void
    {
        $sql = 'DELETE FROM plugin_registry WHERE plugin_id = :pluginId';

        $this->connection->executeStatement($sql, ['pluginId' => $pluginId]);

        $this->logger->info('Plugin unregistered from registry', ['pluginId' => $pluginId]);
    }

    public function getPluginInfo(string $pluginId): ?array
    {
        $sql = 'SELECT * FROM plugin_registry WHERE plugin_id = :pluginId';

        $result = $this->connection->fetchAssociative($sql, ['pluginId' => $pluginId]);

        return $result ?: null;
    }

    public function getAllPlugins(): array
    {
        return $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);

            $sql = 'SELECT * FROM plugin_registry ORDER BY plugin_name ASC';
            return $this->connection->fetchAllAssociative($sql);
        });
    }

    public function getActivePlugins(): array
    {
        $sql = 'SELECT * FROM plugin_registry WHERE status = :status ORDER BY plugin_name ASC';

        return $this->connection->fetchAllAssociative($sql, ['status' => 'active']);
    }

    public function getPluginByName(string $pluginName): ?array
    {
        $sql = 'SELECT * FROM plugin_registry WHERE plugin_name = :pluginName';

        $result = $this->connection->fetchAssociative($sql, ['pluginName' => $pluginName]);

        return $result ?: null;
    }

    public function isPluginActive(string $pluginId): bool
    {
        $sql = 'SELECT COUNT(*) as count FROM plugin_registry WHERE plugin_id = :pluginId AND status = :status';

        $result = $this->connection->fetchAssociative($sql, [
            'pluginId' => $pluginId,
            'status' => 'active'
        ]);

        return (int) $result['count'] > 0;
    }

    public function getPluginDependencies(string $pluginId): array
    {
        $sql = 'SELECT dependencies FROM plugin_registry WHERE plugin_id = :pluginId';

        $result = $this->connection->fetchAssociative($sql, ['pluginId' => $pluginId]);

        if (!$result || !$result['dependencies']) {
            return [];
        }

        return json_decode($result['dependencies'], true) ?: [];
    }

    public function checkDependencyConflicts(string $pluginId): array
    {
        $dependencies = $this->getPluginDependencies($pluginId);
        $conflicts = [];

        foreach ($dependencies as $dependency) {
            $dependencyPlugin = $this->getPluginByName($dependency['name']);
            
            if (!$dependencyPlugin) {
                $conflicts[] = sprintf('Required dependency "%s" is not installed', $dependency['name']);
                continue;
            }

            if (!$this->isPluginActive($dependencyPlugin['plugin_id'])) {
                $conflicts[] = sprintf('Required dependency "%s" is not active', $dependency['name']);
                continue;
            }

            if (isset($dependency['version'])) {
                if (!$this->isVersionCompatible($dependencyPlugin['version'], $dependency['version'])) {
                    $conflicts[] = sprintf(
                        'Dependency "%s" version %s required, but %s is installed',
                        $dependency['name'],
                        $dependency['version'],
                        $dependencyPlugin['version']
                    );
                }
            }
        }

        return $conflicts;
    }

    public function invalidateCache(): void
    {
        $this->cache->delete(self::CACHE_KEY);
        $this->logger->debug('Plugin registry cache invalidated');
    }

    private function isVersionCompatible(string $installedVersion, string $requiredVersion): bool
    {
        // Einfache Version-Kompatibilitätsprüfung
        // In der Praxis würde hier eine komplexere Logik stehen
        return version_compare($installedVersion, $requiredVersion, '>=');
    }

    public function getPluginStatistics(): array
    {
        $sql = 'SELECT 
                    COUNT(*) as total_plugins,
                    SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_plugins,
                    SUM(CASE WHEN status = "inactive" THEN 1 ELSE 0 END) as inactive_plugins,
                    SUM(CASE WHEN status = "installed" THEN 1 ELSE 0 END) as installed_plugins
                FROM plugin_registry';

        $result = $this->connection->fetchAssociative($sql);

        return [
            'total' => (int) $result['total_plugins'],
            'active' => (int) $result['active_plugins'],
            'inactive' => (int) $result['inactive_plugins'],
            'installed' => (int) $result['installed_plugins']
        ];
    }
} 