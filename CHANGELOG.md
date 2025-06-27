# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
und dieses Projekt folgt [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.86-alpha] - 2025-06-27

### Changed
- refactor: Remove unused property 'name' from Client entity


## [0.1.85-alpha] - 2025-06-27

### Added
- feat(Entity): Update Client entity properties to use type hints and improve r...


## [0.1.84-alpha] - 2025-06-27

### Changed
- refactor: Verwende Standard OAuth2-Server-Bibliothek für Token-Generierung u...


## [0.1.83-alpha] - 2025-06-27

### Added
- feat(security): Update password hashing algorithm for User entity in CompanyO...


## [0.1.82-alpha] - 2025-06-27

### Fixed
- fix: Deaktiviere Autowiring und Autoconfiguring für DoctrineUserRepository, ...


## [0.1.81-alpha] - 2025-06-27

### Added
- feat: Set CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence\Doctrin...


## [0.1.80-alpha] - 2025-06-27

### Added
- feat(Infrastructure): Debug-Log hinzugefügt beim Instanziieren von DoctrineU...


## [0.1.79-alpha] - 2025-06-27

### Changed
- refactor: Entfernen der UserRepositoryInterface-Abhängigkeit in DoctrineUser...


## [0.1.78-alpha] - 2025-06-27

### Added
- feat: Hinzufügen von Logger-Argumenten für OAuth2-Controller und DoctrineUs...


## [0.1.77-alpha] - 2025-06-27

### Maintenance
- chore: Update composer.json for 'CompanyOS Core Bundle'


## [0.1.76-alpha] - 2025-06-27

### Added
- feat: Implement Debug-Logging für OAuth2-Anfragen in OAuthController und Doc...


## [0.1.75-alpha] - 2025-06-27

### Changed
- refactor: Update OAuth2 services to use League repositories in CompanyOS Core...


## [0.1.74-alpha] - 2025-06-27

### Added
- feat(DoctrineUserRepository): Debug-Logging hinzufügen und verbessern


## [0.1.73-alpha] - 2025-06-27

### Fixed
- fix: Update namespaces in DoctrineUserRepository and LeagueClientFixture for ...


## [0.1.72-alpha] - 2025-06-27

### Added
- feat(OAuthController): Log actual exception for debugging


## [0.1.71-alpha] - 2025-06-27

### Added
- feat(Infrastructure): Hinzufügen von LeagueClientFixture für OAuth2-Client-...


## [0.1.70-alpha] - 2025-06-27

### Added
- feat(User): User zuerst speichern, bevor Admin-Rolle zugewiesen wird


## [0.1.69-alpha] - 2025-06-27

### Added
- feat(RoleService): Update assignRoleToUser and removeRoleFromUser methods


## [0.1.68-alpha] - 2025-06-27

### Added
- feat(RoleService): Update RoleService to use objects instead of IDs for user ...


## [0.1.67-alpha] - 2025-06-27

### Fixed
- fix: Corrected property access in RoleCommandHandlers


## [0.1.66-alpha] - 2025-06-27

### Added
- feat(RoleRepositoryInterface, DoctrineRoleRepository): Hinzufügen der Method...


## [0.1.65-alpha] - 2025-06-27

### Added
- feat(Infrastructure): Hinzufügen von ClientFixture für die Initialisierung ...


## [0.1.64-alpha] - 2025-06-27

### Maintenance
- chore: Add doctrine/doctrine-fixtures-bundle to require-dev in composer.json


## [0.1.63-alpha] - 2025-06-27

### Changed
- refactor: Remove unnecessary exclusion in services.yaml


## [0.1.62-alpha] - 2025-06-27

### Added
- feat: Neuer Befehl zum Erstellen eines Admin-Benutzers hinzugefügt


## [0.1.61-alpha] - 2025-06-27

### Changed
- refactor: Update directory paths in doctrine.yaml and README.md to use vendor...


## [0.1.60-alpha] - 2025-06-27

### Added
- feat: Integriere Doctrine-Entities und -Konfiguration für Core-Entities Füg...


## [0.1.59-alpha] - 2025-06-27

### Maintenance
- chore: Update composer require version to ^0.1.58-alpha in README.md


## [0.1.58-alpha] - 2025-06-27

### Changed
- refactor: Vereinfachen Sie den Routenpfad für OAuth-Client-Controller


## [0.1.57-alpha] - 2025-06-27

### Added
- feat: Hinzufügen von Route für OAuth2-Clients im CompanyOS Core Bundle


## [0.1.56-alpha] - 2025-06-27

### Changed
- refactor: Update route names in WebhookController for CompanyOS Core Bundle


## [0.1.55-alpha] - 2025-06-27

### Changed
- refactor: Remove unnecessary route annotations from OAuthClientController and...


## [0.1.54-alpha] - 2025-06-26

### Changed
- refactor: Vereinheitliche Pfade für OAuth-Endpunkte in OAuthController


## [0.1.53-alpha] - 2025-06-26

### Added
- feat: Neue Funktion zur Benutzerverwaltung hinzugefügt.


## [0.1.52-alpha] - 2025-06-26

### Added
- feat: Neue Funktion zur Benutzerverwaltung hinzugefügt.


## [0.1.51-alpha] - 2025-06-26

### Changed
- refactor: Update namespace references for CompanyOS Core Bundle in OAuthContr...


## [0.1.50-alpha] - 2025-06-26

### Fixed
- fix: Update namespace for Plugin classes in CompanyOS Core Bundle.


## [0.1.49-alpha] - 2025-06-26

### Added
- feat: Neue Funktion zur Verwaltung von Benutzerrollen hinzugefügt.


## [0.1.48-alpha] - 2025-06-26

### Added
- feat: Update Doctrine, Security, and Validator configurations for CompanyOS C...


## [0.1.47-alpha] - 2025-06-26

### Fixed
- fix: Nur Doctrine-Konfiguration laden, wenn DoctrineBundle aktiv ist


## [0.1.46-alpha] - 2025-06-26

### Maintenance
- chore: Entferne CodeQL-Workflow und aktualisiere PHP-Version auf 8.2


## [0.1.45-alpha] - 2025-06-26

### Added
- feat: Add custom Doctrine types and configuration for CompanyOS Core Bundle


## [0.1.44-alpha] - 2025-06-26

### Added
- feat: Neue Funktion zur Benutzerverwaltung hinzugefügt.


## [0.1.43-alpha] - 2025-06-26

### Added
- feat: Neue Funktion zur Verwaltung von Benutzerrollen hinzugefügt.


## [0.1.42-alpha] - 2025-06-26

### Changed
- refactor: Update namespace in CompanyOSCoreExtension to match Symfony best pr...


## [0.1.41-alpha] - 2025-06-26

### Added
- feat: Konfiguration für Doctrine-Mappings hinzugefügt und Namespace-Änderu...


## [0.1.40-alpha] - 2025-06-26

### Added
- feat: Laden der Doctrine-Konfiguration hinzugefügt


## [0.1.39-alpha] - 2025-06-26

### Changed
- refactor: Update service paths for better organization, increase version to 0...


## [0.1.38-alpha] - 2025-06-26

### Changed
- refactor: Update service paths in services.yaml for better organization, chore: Aktualisiere Pfade für Domain, Application und Infrastructure-Ressourcen, erhöhe Version auf 0.1.35-alpha, chore: Update Alpha-Version auf 0.1.33-alpha in README.md


## [0.1.37-alpha] - 2025-06-26

### Changed
- refactor: Update service paths in services.yaml for better organization


## [0.1.36-alpha] - 2025-06-26

### Maintenance
- chore: Aktualisiere Pfade für Domain, Application und Infrastructure-Ressourcen, erhöhe Version auf 0.1.35-alpha


## [0.1.34-alpha] - 2025-06-26

### Added
- chore: Update Alpha-Version auf 0.1.33-alpha in README.md

### Geplant
- Vollständige Controller-Implementierung
- Unit/Integration Tests
- Datenbank-Migrationen
- Frontend-Assets
- API-Dokumentation

## [0.1.0-alpha] - 2024-01-XX

### Hinzugefügt
- Grundlegende Bundle-Struktur mit DDD-Layer-Architektur
- Domain-Layer mit Auth, User, Role, Plugin, Webhook, Settings und Shared
- Application-Layer für Use Cases, Commands, Queries und DTOs
- Infrastructure-Layer für Persistence, Eventing und Services
- Service-Konfiguration mit DependencyInjection
- Doctrine-Mappings für alle Entities
- Routing-Grundstruktur für alle Controller
- Security-Konfiguration für OAuth2
- Messenger-Konfiguration für CQRS
- Plugin-System-Architektur mit Compiler Pass
- Bundle-Konfiguration mit Extension und Configuration
- Autoloading für alle Namespaces
- README mit Installation und Dokumentation
- MIT-Lizenz

### Technische Details
- Symfony 7.3+ Kompatibilität
- PHP 8.2+ erforderlich
- DDD (Domain-Driven Design) Architektur
- CQRS (Command Query Responsibility Segregation)
- Event-Driven Architecture
- Plugin-System für Erweiterbarkeit
- OAuth2-Authentifizierung
- Webhook-System

### Bekannte Probleme
- Keine Tests implementiert
- Controller nicht vollständig implementiert
- Datenbank-Migrationen fehlen
- Frontend-Assets nicht vollständig
- Dokumentation unvollständig
- Code Coverage fehlt

### Hinweise
- **Alpha-Version**: Nicht für Produktiveinsatz geeignet
- **Entwickler-Version**: Nur für Entwickler und Tester
