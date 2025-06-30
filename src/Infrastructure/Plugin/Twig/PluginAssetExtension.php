<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\Twig;

use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service\PluginManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PluginAssetExtension extends AbstractExtension
{
    public function __construct(
        private PluginManager $pluginManager
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('plugin_css_files', [$this, 'getPluginCssFiles']),
            new TwigFunction('plugin_js_files', [$this, 'getPluginJsFiles']),
            new TwigFunction('plugin_components', [$this, 'getPluginComponents']),
            new TwigFunction('active_plugins', [$this, 'getActivePlugins']),
        ];
    }

    /**
     * Gibt alle CSS-Dateien der aktiven Plugins zurück
     */
    public function getPluginCssFiles(): array
    {
        return $this->pluginManager->getActiveCssFiles();
    }

    /**
     * Gibt alle JavaScript-Dateien der aktiven Plugins zurück
     */
    public function getPluginJsFiles(): array
    {
        return $this->pluginManager->getActiveJavaScriptFiles();
    }

    /**
     * Gibt alle Frontend-Komponenten der aktiven Plugins zurück
     */
    public function getPluginComponents(): array
    {
        return $this->pluginManager->getActiveFrontendComponents();
    }

    /**
     * Gibt alle aktiven Plugins zurück
     */
    public function getActivePlugins(): array
    {
        $plugins = [];
        foreach ($this->pluginManager->getLoadedPlugins() as $pluginName => $plugin) {
            if ($this->pluginManager->isPluginActive($pluginName)) {
                $plugins[] = [
                    'name' => $pluginName,
                    'label' => $plugin->getPluginLabel(),
                    'version' => $plugin->getPluginVersion(),
                ];
            }
        }
        return $plugins;
    }
} 