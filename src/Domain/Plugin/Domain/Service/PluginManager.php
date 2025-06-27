<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service;

use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Entity\Plugin;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepository;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\PluginInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\AbstractPlugin;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use Composer\Autoload\ClassLoader;
use ReflectionClass;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class PluginManager
{
    private array $loadedPlugins = [];
    private array $pluginBundles = [];
    private ?ClassLoader $autoloader = null;
    private ?PluginRepository $pluginRepository = null;

    public function __construct(
        private ContainerInterface $container,
        private LoggerInterface $logger,
        private ?Application $consoleApplication = null,
        private string $pluginDirectories = 'custom/plugins,custom/static-plugins'
    ) {
        $this->initializeAutoloader();
    }

    /**
     * Set plugin repository (called by compiler pass)
     */
    public function setPluginRepository(PluginRepository $pluginRepository): void
    {
        $this->pluginRepository = $pluginRepository;
    }

    /**
     * Set container (called by compiler pass)
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * Initialize Composer autoloader for plugins
     */
    private function initializeAutoloader(): void
    {
        $this->autoloader = new ClassLoader();
        
        // Register plugin directories from configuration
        $directories = explode(',', $this->pluginDirectories);
        foreach ($directories as $directory) {
            $this->registerPluginAutoloader(trim($directory));
        }
        
        $this->autoloader->register();
    }

    /**
     * Register autoloader for plugin directory
     */
    private function registerPluginAutoloader(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $plugins = glob($directory . '/*', GLOB_ONLYDIR);
        
        foreach ($plugins as $pluginPath) {
            $pluginName = basename($pluginPath);
            $composerPath = $pluginPath . '/composer.json';
            
            if (file_exists($composerPath)) {
                $composerData = json_decode(file_get_contents($composerPath), true);
                $autoload = $composerData['autoload'] ?? [];
                
                // Register PSR-4 autoloading
                if (isset($autoload['psr-4'])) {
                    foreach ($autoload['psr-4'] as $namespace => $path) {
                        $fullPath = $pluginPath . '/' . $path;
                        $this->autoloader->addPsr4($namespace, $fullPath);
                    }
                }
                
                // Register PSR-0 autoloading
                if (isset($autoload['psr-0'])) {
                    foreach ($autoload['psr-0'] as $namespace => $path) {
                        $fullPath = $pluginPath . '/' . $path;
                        $this->autoloader->add($namespace, $fullPath);
                    }
                }
            }
        }
    }

    /**
     * Load all plugins
     */
    public function loadPlugins(): void
    {
        $this->loadPluginsFromDirectory('custom/plugins');
        $this->loadPluginsFromDirectory('custom/static-plugins');
        $this->loadInstalledPlugins();
    }

    /**
     * Load plugins from directory
     */
    private function loadPluginsFromDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $plugins = glob($directory . '/*', GLOB_ONLYDIR);
        
        foreach ($plugins as $pluginPath) {
            $this->loadPluginFromPath($pluginPath);
        }
    }

    /**
     * Load plugin from path
     */
    private function loadPluginFromPath(string $pluginPath): ?PluginInterface
    {
        $pluginName = basename($pluginPath);
        $composerPath = $pluginPath . '/composer.json';
        
        if (!file_exists($composerPath)) {
            return null;
        }

        $composerData = json_decode(file_get_contents($composerPath), true);
        $extra = $composerData['extra']['companyos-plugin'] ?? [];
        $pluginClass = $extra['plugin-class'] ?? null;
        
        if (!$pluginClass) {
            return null;
        }

        try {
            // Check if plugin class exists
            if (!class_exists($pluginClass)) {
                return null;
            }

            $reflection = new ReflectionClass($pluginClass);
            
            // Check if class extends AbstractPlugin
            if (!$reflection->isSubclassOf(AbstractPlugin::class)) {
                return null;
            }

            // Create plugin instance
            $plugin = new $pluginClass();
            
            // Check compatibility
            if (!$plugin->isCompatible()) {
                return null;
            }

            $this->loadedPlugins[$plugin->getPluginName()] = $plugin;
            $this->pluginBundles[$plugin->getPluginName()] = $plugin;
            
            return $plugin;
            
        } catch (\Exception $e) {
            // Log error but continue loading other plugins
            $this->logger->error("Failed to load plugin {$pluginName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Load installed plugins from database
     */
    private function loadInstalledPlugins(): void
    {
        if (!$this->pluginRepository) {
            return;
        }
        
        $installedPlugins = $this->pluginRepository->findActive();
        
        foreach ($installedPlugins as $pluginEntity) {
            $pluginName = $pluginEntity->getName();
            
            // Check if plugin is already loaded
            if (isset($this->loadedPlugins[$pluginName])) {
                continue;
            }
            
            // Try to load plugin from configured directories
            $directories = explode(',', $this->pluginDirectories);
            foreach ($directories as $directory) {
                $pluginPath = trim($directory) . '/' . $pluginName;
                if (is_dir($pluginPath)) {
                    $this->loadPluginFromPath($pluginPath);
                    break;
                }
            }
        }
    }

    /**
     * Get all loaded plugins
     */
    public function getLoadedPlugins(): array
    {
        return $this->loadedPlugins;
    }

    /**
     * Get plugin bundles for kernel
     */
    public function getPluginBundles(): array
    {
        return $this->pluginBundles;
    }

    /**
     * Get specific plugin
     */
    public function getPlugin(string $name): ?PluginInterface
    {
        return $this->loadedPlugins[$name] ?? null;
    }

    /**
     * Get plugin by ID
     */
    public function getPluginById(Uuid $pluginId): ?Plugin
    {
        if (!$this->pluginRepository) {
            return null;
        }
        
        return $this->pluginRepository->findById($pluginId);
    }

    /**
     * Install plugin
     */
    public function installPlugin(string $name, string $version, string $author, ?array $meta = null): Plugin
    {
        if (!$this->pluginRepository) {
            throw new \RuntimeException('Plugin repository not available');
        }
        
        if ($this->pluginRepository->existsByName($name)) {
            throw new \InvalidArgumentException('Plugin with this name already exists');
        }

        // Create plugin entity
        $plugin = new Plugin(
            Uuid::random(),
            $name,
            $version,
            $author,
            $meta
        );

        $this->pluginRepository->save($plugin);

        // Load and install plugin
        $pluginInstance = $this->loadPluginFromPath('custom/plugins/' . $name);
        if ($pluginInstance) {
            $pluginInstance->install();
        }

        return $plugin;
    }

    /**
     * Activate plugin
     */
    public function activatePlugin(Uuid $pluginId): void
    {
        if (!$this->pluginRepository) {
            throw new \RuntimeException('Plugin repository not available');
        }
        
        $plugin = $this->pluginRepository->findById($pluginId);
        
        if ($plugin === null) {
            throw new \InvalidArgumentException('Plugin not found');
        }

        $plugin->activate();
        $this->pluginRepository->save($plugin);

        // Activate plugin instance
        $pluginInstance = $this->getPlugin($plugin->getName());
        if ($pluginInstance) {
            $pluginInstance->activate();
        }
    }

    /**
     * Deactivate plugin
     */
    public function deactivatePlugin(Uuid $pluginId): void
    {
        if (!$this->pluginRepository) {
            throw new \RuntimeException('Plugin repository not available');
        }
        
        $plugin = $this->pluginRepository->findById($pluginId);
        
        if ($plugin === null) {
            throw new \InvalidArgumentException('Plugin not found');
        }

        $plugin->deactivate();
        $this->pluginRepository->save($plugin);

        // Deactivate plugin instance
        $pluginInstance = $this->getPlugin($plugin->getName());
        if ($pluginInstance) {
            $pluginInstance->deactivate();
        }
    }

    /**
     * Delete plugin
     */
    public function deletePlugin(Uuid $pluginId): void
    {
        if (!$this->pluginRepository) {
            throw new \RuntimeException('Plugin repository not available');
        }
        
        $plugin = $this->pluginRepository->findById($pluginId);
        
        if ($plugin === null) {
            throw new \InvalidArgumentException('Plugin not found');
        }

        // Uninstall plugin instance
        $pluginInstance = $this->getPlugin($plugin->getName());
        if ($pluginInstance) {
            $pluginInstance->uninstall();
        }

        $plugin->delete();
        $this->pluginRepository->delete($plugin);
    }

    /**
     * Update plugin
     */
    public function updatePlugin(Uuid $pluginId, string $newVersion): void
    {
        if (!$this->pluginRepository) {
            throw new \RuntimeException('Plugin repository not available');
        }
        
        $plugin = $this->pluginRepository->findById($pluginId);
        
        if ($plugin === null) {
            throw new \InvalidArgumentException('Plugin not found');
        }

        $oldVersion = $plugin->getVersion();
        $plugin->update($newVersion);
        $this->pluginRepository->save($plugin);

        // Update plugin instance
        $pluginInstance = $this->getPlugin($plugin->getName());
        if ($pluginInstance) {
            $pluginInstance->update($oldVersion);
        }
    }

    /**
     * Get plugin entity
     */
    public function getPluginEntity(Uuid $pluginId): ?Plugin
    {
        if (!$this->pluginRepository) {
            return null;
        }
        
        return $this->pluginRepository->findById($pluginId);
    }

    /**
     * Get all plugin entities
     */
    public function getAllPluginEntities(): array
    {
        if (!$this->pluginRepository) {
            return [];
        }
        
        return $this->pluginRepository->findAll();
    }

    /**
     * Get active plugin entities
     */
    public function getActivePluginEntities(): array
    {
        if (!$this->pluginRepository) {
            return [];
        }
        
        return $this->pluginRepository->findActive();
    }

    /**
     * Build plugin container
     */
    public function buildPluginContainer(ContainerBuilder $container): void
    {
        foreach ($this->loadedPlugins as $plugin) {
            $plugin->build($container);
        }
    }

    /**
     * Load plugin routes
     */
    public function loadPluginRoutes(): RouteCollection
    {
        $routeCollection = new RouteCollection();
        
        foreach ($this->loadedPlugins as $plugin) {
            $routes = $plugin->getRoutes();
            
            if (!empty($routes)) {
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
        }
        
        return $routeCollection;
    }

    /**
     * Get plugin configuration
     */
    public function getPluginConfiguration(string $pluginName): array
    {
        $plugin = $this->getPlugin($pluginName);
        
        if ($plugin) {
            return $plugin->getConfiguration();
        }
        
        return [];
    }

    /**
     * Get plugin assets
     */
    public function getPluginAssets(string $pluginName): array
    {
        $plugin = $this->getPlugin($pluginName);
        return $plugin ? $plugin->getAssets() : [];
    }

    /**
     * Validate plugin compatibility
     */
    public function validatePluginCompatibility(string $pluginName): bool
    {
        $plugin = $this->getPlugin($pluginName);
        
        if ($plugin) {
            return $plugin->isCompatible();
        }
        
        return false;
    }

    /**
     * Get plugin dependencies
     */
    public function getPluginDependencies(string $pluginName): array
    {
        $plugin = $this->getPlugin($pluginName);
        
        if ($plugin) {
            return $plugin->getRequirements();
        }
        
        return [];
    }

    /**
     * Get configured plugin directories
     */
    public function getPluginDirectories(): string
    {
        return $this->pluginDirectories;
    }

    /**
     * Update plugin version
     */
    public function updatePluginVersion(Uuid $pluginId, string $newVersion): void
    {
        if (!$this->pluginRepository) {
            throw new \RuntimeException('Plugin repository not available');
        }
        
        $plugin = $this->pluginRepository->findById($pluginId);
        
        if (!$plugin) {
            throw new \InvalidArgumentException('Plugin not found');
        }

        $plugin->updateVersion($newVersion);
        $this->pluginRepository->save($plugin);
    }

    /**
     * Run plugin migrations safely
     */
    public function runPluginMigrations(string $pluginName): bool
    {
        $plugin = $this->getPlugin($pluginName);
        if (!$plugin) {
            $this->logger->error("Plugin {$pluginName} not found for migration");
            return false;
        }

        $migrationsPath = $plugin->getPath() . '/Resources/migrations';
        if (!is_dir($migrationsPath)) {
            $this->logger->info("No migrations found for plugin {$pluginName}");
            return true;
        }

        if (!$this->consoleApplication) {
            $this->logger->warning("Console application not available for plugin {$pluginName} migrations");
            return false;
        }

        try {
            $input = new ArrayInput([
                'command' => 'doctrine:migrations:migrate',
                '--path' => $migrationsPath,
                '--no-interaction' => true,
                '--allow-no-migration' => true
            ]);
            
            $output = new BufferedOutput();
            $exitCode = $this->consoleApplication->run($input, $output);
            
            if ($exitCode === 0) {
                $this->logger->info("Migrations executed successfully for plugin {$pluginName}");
                return true;
            } else {
                $this->logger->error("Migration failed for plugin {$pluginName}: " . $output->fetch());
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->error("Migration error for plugin {$pluginName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all frontend components from active plugins
     */
    public function getActiveFrontendComponents(): array
    {
        $components = [];
        
        foreach ($this->loadedPlugins as $pluginName => $plugin) {
            if ($this->isPluginActive($pluginName)) {
                $pluginComponents = $plugin->getFrontendComponents();
                foreach ($pluginComponents as $componentName => $componentConfig) {
                    $components[$componentName] = array_merge($componentConfig, [
                        'plugin' => $pluginName
                    ]);
                }
            }
        }
        
        return $components;
    }

    /**
     * Get all JavaScript files from active plugins
     */
    public function getActiveJavaScriptFiles(): array
    {
        $files = [];
        
        foreach ($this->loadedPlugins as $pluginName => $plugin) {
            if ($this->isPluginActive($pluginName)) {
                $pluginFiles = $plugin->getJavaScriptFiles();
                foreach ($pluginFiles as $file) {
                    $files[] = [
                        'plugin' => $pluginName,
                        'file' => $file,
                        'path' => $this->getPluginAssetPath($pluginName, $file)
                    ];
                }
            }
        }
        
        return $files;
    }

    /**
     * Get all CSS files from active plugins
     */
    public function getActiveCssFiles(): array
    {
        $files = [];
        
        foreach ($this->loadedPlugins as $pluginName => $plugin) {
            if ($this->isPluginActive($pluginName)) {
                $pluginFiles = $plugin->getCssFiles();
                foreach ($pluginFiles as $file) {
                    $files[] = [
                        'plugin' => $pluginName,
                        'file' => $file,
                        'path' => $this->getPluginAssetPath($pluginName, $file)
                    ];
                }
            }
        }
        
        return $files;
    }

    /**
     * Get all Vue components from active plugins
     */
    public function getActiveVueComponents(): array
    {
        $components = [];
        
        foreach ($this->loadedPlugins as $pluginName => $plugin) {
            if ($this->isPluginActive($pluginName)) {
                $pluginComponents = $plugin->getVueComponents();
                foreach ($pluginComponents as $componentName => $componentConfig) {
                    $components[$componentName] = array_merge($componentConfig, [
                        'plugin' => $pluginName
                    ]);
                }
            }
        }
        
        return $components;
    }

    /**
     * Check if plugin is active
     */
    private function isPluginActive(string $pluginName): bool
    {
        if (!$this->pluginRepository) {
            return false;
        }
        
        $pluginEntity = $this->pluginRepository->findByName($pluginName);
        return $pluginEntity && $pluginEntity->isActive();
    }

    /**
     * Check if plugin is installed
     */
    public function isPluginInstalled(string $pluginName): bool
    {
        if (!$this->pluginRepository) {
            return false;
        }
        
        $pluginEntity = $this->pluginRepository->findByName($pluginName);
        return $pluginEntity !== null;
    }

    /**
     * Check if plugin is installed AND active
     */
    public function isPluginInstalledAndActive(string $pluginName): bool
    {
        if (!$this->pluginRepository) {
            return false;
        }
        
        $pluginEntity = $this->pluginRepository->findByName($pluginName);
        return $pluginEntity !== null && $pluginEntity->isActive();
    }

    /**
     * Get plugin status (installed and active)
     */
    public function getPluginStatus(string $pluginName): array
    {
        if (!$this->pluginRepository) {
            return [
                'installed' => false,
                'active' => false,
                'message' => 'Plugin repository not available'
            ];
        }
        
        $pluginEntity = $this->pluginRepository->findByName($pluginName);
        $installed = $pluginEntity !== null;
        $active = $installed && $pluginEntity->isActive();
        
        return [
            'installed' => $installed,
            'active' => $active,
            'name' => $pluginName
        ];
    }

    /**
     * Get plugin asset path
     */
    private function getPluginAssetPath(string $pluginName, string $file): string
    {
        // Try to find plugin directory
        $directories = explode(',', $this->pluginDirectories);
        foreach ($directories as $directory) {
            $pluginPath = trim($directory) . '/' . $pluginName;
            if (is_dir($pluginPath)) {
                return $pluginPath . '/' . $file;
            }
        }
        
        return '';
    }
} 