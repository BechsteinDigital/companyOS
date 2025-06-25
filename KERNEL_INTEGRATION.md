# Kernel Integration

## Übersicht

Das CompanyOS Core Bundle erweitert **nicht** den Symfony-Kernel direkt. Stattdessen wird die Plugin-Funktionalität über Services und Compiler Passes bereitgestellt.

## Plugin-System

Das Plugin-System funktioniert vollständig über Services:

- **PluginManager**: Verwaltet das Laden und Verwalten von Plugins
- **PluginCompilerPass**: Konfiguriert die Plugin-Services zur Compile-Zeit
- **PluginServiceLoader**: Lädt Plugin-Services dynamisch
- **PluginRouteSubscriber**: Registriert Plugin-Routen

## Doctrine-Konfiguration

### Im Hauptprojekt (config/packages/doctrine.yaml)

```yaml
doctrine:
    orm:
        mappings:
            CompanyOSCore:
                type: attribute
                is_bundle: true
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain'
                prefix: 'CompanyOS\Domain'
                alias: CompanyOS
    migrations:
        paths:
            'CompanyOS\Migrations':
                - '%kernel.project_dir%/vendor/companyos/core/src/Migrations'
```

### Bundle registrieren

```php
// config/bundles.php
return [
    // ... andere Bundles
    CompanyOS\CompanyOSCoreBundle::class => ['all' => true],
];
```

## Kernel-Erweiterung (Optional)

Falls Sie den Kernel im Hauptprojekt erweitern möchten, können Sie dies tun:

### 1. Kernel im Hauptprojekt erweitern

```php
<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // Hier können Sie zusätzliche Kernel-Funktionalität hinzufügen
    // Das Plugin-System funktioniert bereits über Services
}
```

### 2. Plugin-Bundles manuell registrieren (falls nötig)

```php
<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $bundles = require $this->getProjectDir().'/config/bundles.php';
        
        foreach ($bundles as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }

        // Plugin-Bundles dynamisch laden (falls nötig)
        if ($this->container && $this->container->has('CompanyOS\Core\Plugin\Domain\Service\PluginManager')) {
            $pluginManager = $this->container->get('CompanyOS\Core\Plugin\Domain\Service\PluginManager');
            $pluginBundles = $pluginManager->getPluginBundles();
            
            foreach ($pluginBundles as $pluginBundle) {
                yield $pluginBundle;
            }
        }
    }
}
```

## Empfohlener Ansatz

**Wir empfehlen, den Kernel nicht zu erweitern**, da:

1. Das Plugin-System bereits vollständig über Services funktioniert
2. Routen werden automatisch über den `PluginRouteSubscriber` registriert
3. Services werden automatisch über den `PluginServiceLoader` geladen
4. Die Architektur ist sauberer und wartbarer

## Plugin-Loading

Plugins werden automatisch geladen:

1. **Bundle Boot**: Der `CompanyOSCoreBundle` lädt Plugins beim Boot
2. **Service Discovery**: Plugins werden über Composer Autoloader entdeckt
3. **Route Registration**: Plugin-Routen werden automatisch registriert
4. **Service Loading**: Plugin-Services werden automatisch geladen

## Konfiguration

Die Plugin-Konfiguration erfolgt über Parameter:

```yaml
# config/services.yaml
parameters:
    companyos.plugin.directories: 'custom/plugins,custom/static-plugins'
```

## Troubleshooting

### Plugin wird nicht geladen

1. Prüfen Sie, ob das Plugin im konfigurierten Verzeichnis liegt
2. Prüfen Sie die `composer.json` des Plugins
3. Prüfen Sie die Logs auf Fehler

### Routen funktionieren nicht

1. Prüfen Sie, ob der `PluginRouteSubscriber` registriert ist
2. Prüfen Sie die Plugin-Routen-Konfiguration
3. Cache leeren: `php bin/console cache:clear`

### Services nicht verfügbar

1. Prüfen Sie, ob der `PluginServiceLoader` funktioniert
2. Prüfen Sie die Service-Konfiguration des Plugins
3. Cache leeren: `php bin/console cache:clear`

### Doctrine-Fehler

1. Stellen Sie sicher, dass die Doctrine-Konfiguration im Hauptprojekt korrekt ist
2. Prüfen Sie, ob die Entity-Pfade korrekt sind
3. Cache leeren: `php bin/console cache:clear` 