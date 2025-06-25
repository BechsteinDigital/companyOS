<?php

namespace CompanyOS\Domain\Plugin\Domain;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use ReflectionClass;

abstract class AbstractPlugin extends Bundle implements PluginInterface
{
    protected string $pluginName;
    protected string $pluginVersion;
    protected string $pluginAuthor;
    protected string $pluginDescription;
    protected ?string $pluginHomepage = null;
    protected ?string $pluginLicense = null;
    protected ?string $pluginIcon = null;
    protected string $pluginLabel;
    protected string $pluginManufacturer;
    protected ?string $pluginManufacturerUrl = null;
    protected ?string $pluginSupportUrl = null;
    protected ?array $pluginChangelog = null;
    protected array $pluginRequirements = [];

    public function __construct()
    {
        $this->initializePlugin();
    }

    /**
     * Initialize plugin with metadata from composer.json
     */
    protected function initializePlugin(): void
    {
        $composerPath = $this->getPath() . '/composer.json';
        
        if (file_exists($composerPath)) {
            $composerData = json_decode(file_get_contents($composerPath), true);
            $extra = $composerData['extra']['companyos-plugin'] ?? [];
            
            $this->pluginName = $composerData['name'] ?? '';
            $this->pluginVersion = $composerData['version'] ?? '1.0.0';
            $this->pluginAuthor = $composerData['authors'][0]['name'] ?? 'Unknown';
            $this->pluginDescription = $composerData['description'] ?? '';
            $this->pluginHomepage = $composerData['homepage'] ?? null;
            $this->pluginLicense = $composerData['license'] ?? null;
            $this->pluginLabel = $extra['label'] ?? $this->pluginName;
            $this->pluginManufacturer = $extra['manufacturer'] ?? 'Unknown';
            $this->pluginManufacturerUrl = $extra['manufacturer-url'] ?? null;
            $this->pluginSupportUrl = $extra['support-url'] ?? null;
            $this->pluginChangelog = $extra['changelog'] ?? null;
            $this->pluginRequirements = $extra['requirements'] ?? [];
        }
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getVersion(): string
    {
        return $this->pluginVersion;
    }

    public function getAuthor(): string
    {
        return $this->pluginAuthor;
    }

    public function getDescription(): string
    {
        return $this->pluginDescription;
    }

    public function getHomepage(): ?string
    {
        return $this->pluginHomepage;
    }

    public function getLicense(): ?string
    {
        return $this->pluginLicense;
    }

    public function getIcon(): ?string
    {
        return $this->pluginIcon;
    }

    public function getLabel(): string
    {
        return $this->pluginLabel ?? $this->getPluginName();
    }

    public function getManufacturer(): string
    {
        return $this->pluginManufacturer;
    }

    public function getManufacturerUrl(): ?string
    {
        return $this->pluginManufacturerUrl;
    }

    public function getSupportUrl(): ?string
    {
        return $this->pluginSupportUrl;
    }

    public function getChangelog(): ?array
    {
        return $this->pluginChangelog;
    }

    public function getRequirements(): array
    {
        return $this->pluginRequirements;
    }

    public function isCompatible(): bool
    {
        // Check PHP version
        $phpVersion = $this->pluginRequirements['php'] ?? '>=8.2';
        if (!version_compare(PHP_VERSION, $phpVersion, '>=')) {
            return false;
        }

        // Check Symfony version
        $symfonyVersion = $this->pluginRequirements['symfony'] ?? '>=7.3';
        if (!version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, $symfonyVersion, '>=')) {
            return false;
        }

        return true;
    }

    public function install(): void
    {
        // Default implementation - can be overridden
        $this->installDatabase();
        $this->installAssets();
    }

    public function uninstall(): void
    {
        // Default implementation - can be overridden
        $this->uninstallDatabase();
        $this->uninstallAssets();
    }

    public function activate(): void
    {
        // Default implementation - can be overridden
    }

    public function deactivate(): void
    {
        // Default implementation - can be overridden
    }

    public function update(string $oldVersion): void
    {
        // Default implementation - can be overridden
        $this->updateDatabase($oldVersion);
        $this->updateAssets();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        
        // Load plugin services
        $this->loadServices($container);
        
        // Load plugin configuration
        $this->loadConfiguration($container);
    }

    public function getConfiguration(): array
    {
        $configPath = $this->getPath() . '/Resources/config/config.yaml';
        
        if (file_exists($configPath)) {
            return Yaml::parseFile($configPath);
        }
        
        return [];
    }

    public function getRoutes(): array
    {
        $routesPath = $this->getPath() . '/Resources/config/routes.yaml';
        
        if (file_exists($routesPath)) {
            return Yaml::parseFile($routesPath);
        }
        
        return [];
    }

    public function getServices(): array
    {
        $servicesPath = $this->getPath() . '/Resources/config/services.yaml';
        
        if (file_exists($servicesPath)) {
            return Yaml::parseFile($servicesPath);
        }
        
        return [];
    }

    public function getAssets(): array
    {
        $assetsPath = $this->getPath() . '/Resources/public';
        
        if (is_dir($assetsPath)) {
            return [
                'css' => glob($assetsPath . '/css/*.css'),
                'js' => glob($assetsPath . '/js/*.js'),
                'images' => glob($assetsPath . '/images/*')
            ];
        }
        
        return [];
    }

    /**
     * Load plugin services into container
     */
    protected function loadServices(ContainerBuilder $container): void
    {
        $servicesPath = $this->getPath() . '/Resources/config/services.yaml';
        
        if (file_exists($servicesPath)) {
            $loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, new FileLocator());
            $loader->load($servicesPath);
        }
    }

    /**
     * Load plugin configuration into container
     */
    protected function loadConfiguration(ContainerBuilder $container): void
    {
        $configPath = $this->getPath() . '/Resources/config/config.yaml';
        
        if (file_exists($configPath)) {
            $config = Yaml::parseFile($configPath);
            $container->setParameter($this->getPluginName() . '.config', $config);
        }
    }

    /**
     * Install database migrations
     */
    protected function installDatabase(): void
    {
        $migrationsPath = $this->getPath() . '/Resources/migrations';
        
        if (is_dir($migrationsPath)) {
            // Use Symfony Console Application instead of exec()
            // This should be handled by the PluginManager with proper dependency injection
            // For now, we'll throw an exception to force proper implementation
            throw new \RuntimeException(
                'Database migrations should be handled by PluginManager with proper Console Application injection. ' .
                'This method is deprecated and will be removed.'
            );
        }
    }

    /**
     * Uninstall database migrations
     */
    protected function uninstallDatabase(): void
    {
        // Implementation for database cleanup
    }

    /**
     * Update database migrations
     */
    protected function updateDatabase(string $oldVersion): void
    {
        $this->installDatabase();
    }

    /**
     * Install plugin assets
     */
    protected function installAssets(): void
    {
        $assetsPath = $this->getPath() . '/Resources/public';
        $publicPath = 'public/plugins/' . $this->getPluginName();
        
        if (is_dir($assetsPath) && !is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
            $this->copyDirectory($assetsPath, $publicPath);
        }
    }

    /**
     * Uninstall plugin assets
     */
    protected function uninstallAssets(): void
    {
        $publicPath = 'public/plugins/' . $this->getPluginName();
        
        if (is_dir($publicPath)) {
            $this->removeDirectory($publicPath);
        }
    }

    /**
     * Update plugin assets
     */
    protected function updateAssets(): void
    {
        $this->installAssets();
    }

    /**
     * Copy directory recursively
     */
    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $sourcePath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;
            
            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }
        closedir($dir);
    }

    /**
     * Remove directory recursively
     */
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