# CompanyOS Core Bundle

Ein vollständiges Symfony-Bundle für das CompanyOS-System mit Plugin-Architektur, Authentifizierung, Benutzerverwaltung und API-First-Design.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/companyos/core.svg)](https://packagist.org/packages/companyos/core)
[![Total Downloads](https://img.shields.io/packagist/dt/companyos/core.svg)](https://packagist.org/packages/companyos/core)
[![License](https://img.shields.io/packagist/l/companyos/core.svg)](https://packagist.org/packages/companyos/core)

## ⚠️ Alpha Version

**Dies ist eine Alpha-Version (0.1.142-alpha

### Was funktioniert:
- ✅ Grundlegende Bundle-Struktur (DDD-Layer)
- ✅ Service-Konfiguration
- ✅ Doctrine-Mappings
- ✅ Routing-Grundstruktur
- ✅ Plugin-System-Architektur
- ✅ API-First-Design

### Was noch fehlt:
- ❌ Vollständige Controller-Implementierung
- ❌ Unit/Integration Tests
- ❌ Datenbank-Migrationen
- ❌ Vollständige Dokumentation
- ❌ Code Coverage

## Architektur

Das CoreBundle folgt einer **API-First-Architektur**:

- **Reine API**: Keine UI, keine Assets, keine Templates
- **Business-Logik**: Domain-Logik, Application-Layer, Infrastructure
- **Plugin-Framework**: Event- und Service-Extension für Core und API
- **Modular**: Kann unabhängig vom BackendBundle verwendet werden

## Features

- **Plugin-System**: Dynamisches Laden und Verwalten von Plugins
- **OAuth2-Authentifizierung**: Vollständige OAuth2-Implementierung mit Password Grant
- **Benutzerverwaltung**: CRUD-Operationen für Benutzer
- **Rollen- und Berechtigungssystem**: Flexible Zugriffskontrolle
- **Webhook-System**: Event-basierte Webhook-Integration
- **Event-Driven Architecture**: Domain Events und Event Store
- **API-First**: Alle Funktionen über REST-API verfügbar

## Installation

### ⚠️ Nur für Entwickler und Tester

```bash
composer require companyos/core:^0.1.142-alpha
```

### Bundle registrieren

```php
// config/bundles.php
return [
    // ... andere Bundles
    CompanyOS\CompanyOSCoreBundle::class => ['all' => true],
];
```

### Konfiguration

```yaml
# config/packages/companyos_core.yaml
companyos_core:
    plugin:
        directories: 'custom/plugins,custom/static-plugins'
    auth:
        oauth2:
            enabled: true
            client_id: 'your-client-id'
            client_secret: 'your-client-secret'
```

## Plugin-System

Das Plugin-System funktioniert vollständig über Services und erfordert **keine Kernel-Erweiterung**:

- **Automatisches Plugin-Loading**: Plugins werden beim Bundle-Boot geladen
- **Service-basierte Architektur**: Alle Plugin-Funktionen über Services
- **Dynamische Routen**: Plugin-Routen werden automatisch registriert
- **Service-Discovery**: Plugin-Services werden automatisch geladen

### Plugin erstellen

```php
<?php

namespace MyPlugin;

use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\AbstractPlugin;

class MyPlugin extends AbstractPlugin
{
    public function getPluginName(): string
    {
        return 'my-plugin';
    }

    public function getVersion(): string
    {
        return '0.1.142-alpha
    }

    public function getAuthor(): string
    {
        return 'Your Name';
    }

    public function getRoutes(): array
    {
        return [
            'my_plugin_route' => '/my-route'
        ];
    }
}
```

### Plugin-Konfiguration

```json
{
    "name": "my/plugin",
    "type": "companyos-plugin",
    "autoload": {
        "psr-4": {
            "MyPlugin\\": "src/"
        }
    },
    "extra": {
        "companyos-plugin": {
            "plugin-class": "MyPlugin\\MyPlugin"
        }
    }
}
```

## API-Endpunkte

### Authentifizierung

- `POST /api/auth/login` - Benutzer-Login
- `POST /api/auth/refresh` - Token erneuern
- `POST /api/auth/logout` - Logout
- `GET /api/auth/profile` - Benutzer-Profil

### Benutzerverwaltung

- `GET /api/users` - Benutzer auflisten
- `POST /api/users` - Benutzer erstellen
- `GET /api/users/{id}` - Benutzer abrufen
- `PUT /api/users/{id}` - Benutzer aktualisieren
- `DELETE /api/users/{id}` - Benutzer löschen

### Plugin-Verwaltung

- `GET /api/plugins` - Plugins auflisten
- `POST /api/plugins/{name}/install` - Plugin installieren
- `POST /api/plugins/{id}/activate` - Plugin aktivieren
- `POST /api/plugins/{id}/deactivate` - Plugin deaktivieren
- `DELETE /api/plugins/{id}` - Plugin löschen

## Konfiguration

### Plugin-Verzeichnisse

```yaml
parameters:
    companyos.plugin.directories: 'custom/plugins,custom/static-plugins'
```

### OAuth2-Konfiguration

```yaml
companyos_core:
    auth:
        oauth2:
            enabled: true
            access_token_ttl: 3600
            refresh_token_ttl: 1209600
```

### Webhook-Konfiguration

```yaml
companyos_core:
    webhook:
        enabled: true
        max_retries: 3
        timeout: 30
```

## Entwicklung

### Bundle-Struktur

```
CompanyOSCoreBundle/
├── Domain/           # Domain-Logik (Entities, Value Objects, Events)
│   ├── Auth/Domain/
│   ├── User/Domain/
│   ├── Role/Domain/
│   ├── Plugin/Domain/
│   ├── Webhook/Domain/
│   ├── Settings/Domain/
│   └── Shared/Domain/
├── Application/      # Use Cases, Commands, Queries, DTOs
│   ├── Auth/
│   ├── User/
│   ├── Role/
│   ├── Plugin/
│   ├── Webhook/
│   ├── Settings/
│   └── Shared/
├── Infrastructure/   # Persistence, Eventing, Services
│   ├── Auth/
│   ├── User/
│   ├── Role/
│   ├── Plugin/
│   ├── Webhook/
│   ├── Settings/
│   └── Shared/
├── DependencyInjection/
├── Resources/
└── ...
```

### Tests

```bash
# Unit Tests (noch nicht implementiert)
vendor/bin/phpunit

# Frontend Tests (noch nicht implementiert)
npm test
```

## Kernel-Integration

Das Bundle erweitert **nicht** den Symfony-Kernel. Die Plugin-Funktionalität wird vollständig über Services bereitgestellt. Siehe [KERNEL_INTEGRATION.md](KERNEL_INTEGRATION.md) für Details.

## Roadmap

### Version 0.1.142-alpha
- [ ] Vollständige Controller-Implementierung
- [ ] Basis-Tests (Unit/Integration)
- [ ] Datenbank-Migrationen
- [ ] Verbesserte Dokumentation

### Version 0.1.142-alpha
- [ ] Vollständige API-Implementierung
- [ ] Frontend-Assets
- [ ] Code Coverage > 80%
- [ ] CI/CD Pipeline

### Version 0.1.142-alpha
- [ ] Production-ready
- [ ] Vollständige Tests
- [ ] Vollständige Dokumentation
- [ ] Performance-Optimierungen

## Third-Party-Lizenzen

Dieses Bundle verwendet verschiedene Open-Source-Bibliotheken und Frameworks. Hier sind die wichtigsten Dependencies und deren Lizenzen:

### Backend Dependencies

- **Symfony Framework** (MIT) - PHP Web Framework
- **Doctrine ORM** (MIT) - Object-Relational Mapping
- **League OAuth2 Server** (MIT) - OAuth2 Server Implementation
- **Nelmio API Doc Bundle** (MIT) - API Documentation Generator
- **PHPUnit** (BSD-3-Clause) - Testing Framework

### Frontend Dependencies

- **Vue.js** (MIT) - Progressive JavaScript Framework
- **CoreUI** (MIT) - Bootstrap-based Admin Template
- **Bootstrap** (MIT) - CSS Framework
- **Webpack Encore** (MIT) - Asset Management

### Vollständige Dependency-Liste

Für eine vollständige Liste aller verwendeten Pakete und deren Lizenzen siehe:
- `composer.json` für PHP-Dependencies
- `package.json` für JavaScript-Dependencies

Alle verwendeten Pakete sind Open-Source und unter permissiven Lizenzen (hauptsächlich MIT) verfügbar.

## Lizenz

MIT License - siehe [LICENSE](LICENSE) Datei.

## Support

Bei Fragen oder Problemen erstellen Sie ein Issue im Repository oder kontaktieren Sie das Entwicklungsteam.

## Contributing

1. Fork das Repository
2. Erstelle einen Feature-Branch (`git checkout -b feature/amazing-feature`)
3. Committe deine Änderungen (`git commit -m 'Add some amazing feature'`)
4. Push zum Branch (`git push origin feature/amazing-feature`)
5. Öffne einen Pull Request

## Doctrine-Integration

Das Bundle stellt automatisch alle Core-Entities für Doctrine bereit. Um die Entities in die Datenbank zu schreiben:

### 1. Doctrine-Konfiguration im Hauptprojekt

Füge folgende Konfiguration in dein `config/packages/doctrine.yaml` hinzu:

```yaml
doctrine:
    dbal:
        types:
            uuid: CompanyOS\Bundle\CoreBundle\Infrastructure\Persistence\Doctrine\UuidType
            email: CompanyOS\Bundle\CoreBundle\Infrastructure\Persistence\Doctrine\EmailType
    orm:
        mappings:
            CompanyOSCore:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain'
                prefix: 'CompanyOS\Bundle\CoreBundle\Domain'
                alias: CompanyOS
            CompanyOSUser:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain/User/Domain/Entity'
                prefix: 'CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity'
                alias: CompanyOSUser
            CompanyOSAuth:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain/Auth/Domain/Entity'
                prefix: 'CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity'
                alias: CompanyOSAuth
            CompanyOSRole:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain/Role/Domain/Entity'
                prefix: 'CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity'
                alias: CompanyOSRole
            CompanyOSPlugin:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain/Plugin/Domain/Entity'
                prefix: 'CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Entity'
                alias: CompanyOSPlugin
            CompanyOSWebhook:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain/Webhook/Domain/Entity'
                prefix: 'CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Entity'
                alias: CompanyOSWebhook
            CompanyOSSettings:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain/Settings/Domain/Entity'
                prefix: 'CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Entity'
                alias: CompanyOSSettings
            CompanyOSEvent:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/vendor/companyos/core/src/Domain/Event'
                prefix: 'CompanyOS\Bundle\CoreBundle\Domain\Event'
                alias: CompanyOSEvent
```

### 2. Datenbank-Schema erstellen

```bash
# Schema erstellen
bin/console doctrine:schema:create

# Oder Schema aktualisieren
bin/console doctrine:schema:update --force

# Oder Migrationen generieren
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

### Verfügbare Entities

Das Bundle stellt folgende Entities bereit:

- **User**: Benutzer-Verwaltung (`users` Tabelle)
- **Role**: Rollen-Verwaltung (`roles` Tabelle)
- **UserRole**: Benutzer-Rollen-Zuordnung (`user_roles` Tabelle)
- **Client**: OAuth2-Clients (`oauth_clients` Tabelle)
- **AccessToken**: OAuth2-Access-Tokens (`oauth_access_tokens` Tabelle)
- **RefreshToken**: OAuth2-Refresh-Tokens (`oauth_refresh_tokens` Tabelle)
- **Plugin**: Plugin-Verwaltung (`plugins` Tabelle)
- **Webhook**: Webhook-Verwaltung (`webhooks` Tabelle)
- **CompanySettings**: Unternehmens-Einstellungen (`company_settings` Tabelle)
- **StoredEvent**: Event Store (`stored_events` Tabelle) 