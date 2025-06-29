# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
und dieses Projekt folgt [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.117-alpha] - 2025-06-28

### Changed
- refactor: Vereinfache die Konfiguration des OAuth2 UserRepository und entfern...


## [0.1.116-alpha] - 2025-06-28

### Added
- feat: Implementiere OAuth2 UserResolve Event Listener und UserConverter Regis...


## [0.1.115-alpha] - 2025-06-28

### Added
- feat: Add new methods to retrieve frontend components, JavaScript files, CSS ...


## [0.1.114-alpha] - 2025-06-27

### Added
- feat: Neue Funktion zur Benutzerverwaltung hinzugefügt.


## [0.1.113-alpha] - 2025-06-27

### Added
- feat: Implement Input-Sanitization und Validierung für E-Mail in DoctrineUse...


## [0.1.112-alpha] - 2025-06-27

### Added
- feat(CoreBundle): Hinzufügen von benutzerdefiniertem User-Provider für Roll...


## [0.1.111-alpha] - 2025-06-27

### Added
- feat(UserController): Add endpoint to retrieve current user profile


## [0.1.110-alpha] - 2025-06-27

### Added
- feat(Application/User/Query): Aktualisiere GetUserQuery-Klasse


## [0.1.109-alpha] - 2025-06-27

### Changed
- refactor: Ändere den Parameternamen von userId zu id in GetUserQuery


## [0.1.108-alpha] - 2025-06-27

### Added
- feat(UserController): Verbessere Authentifizierung für Profilabruf


## [0.1.107-alpha] - 2025-06-27

### Added
- feat: Neue Funktion zur Verwaltung von Unternehmensdaten hinzugefügt.


## [0.1.106-alpha] - 2025-06-27

### Changed
- refactor: Remove unnecessary configuration in CompanyOS Core Bundle


## [0.1.105-alpha] - 2025-06-27

### Added
- refactor(security): Remove custom authenticator from main firewall configuration


## [0.1.104-alpha] - 2025-06-27

### Added
- feat: Registriere UserRepository für OAuth2-Server in services.yaml


## [0.1.103-alpha] - 2025-06-27

### Added
- feat(api): Update API routes for User, Role, Plugin, Webhook, and Settings in...


## [0.1.102-alpha] - 2025-06-27

### Added
- feat(core): Update routes configuration for User, Role, Plugin, System, Webho...


## [0.1.101-alpha] - 2025-06-27

### Fixed
- fix: Removed unnecessary OAuth2 controller services from services.yaml


## [0.1.100-alpha] - 2025-06-27

### Changed
- refactor: Mark OAuth2 Controller Services as public for League Bundle registr...


## [0.1.99-alpha] - 2025-06-27

### Added
- feat: Mark OAuth2 Controller Services as public in services.yaml


## [0.1.98-alpha] - 2025-06-27

### Added
- feat: Hinzufügen der 'api_users_profile'-Route für die Profilanzeige und En...


## [0.1.97-alpha] - 2025-06-27

### Added
- feat: Hinzufügen von benannten Routen für Plugin- und Rollen-API-Endpunkte


## [0.1.96-alpha] - 2025-06-27

### Added
- feat: Hinzufügen von zusätzlichen Passwort-Test-Logs in DoctrineUserRepository


## [0.1.95-alpha] - 2025-06-27

### Added
- feat(DoctrineUserRepository): Hinzufügen von Debug-Logging am Anfang der Met...


## [0.1.94-alpha] - 2025-06-27

### Added
- feat(Uuid): Add getValue method to return the UUID value


## [0.1.93-alpha] - 2025-06-27

### Added
- feat: Register CompanyOS UserRepository as OAuth2 UserRepository Alias Regist...


## [0.1.92-alpha] - 2025-06-27

### Added
- feat(security): Aktualisiere Sicherheitskonfiguration für öffentliche Endpunkte


## [0.1.91-alpha] - 2025-06-27

### Fixed
- fix: Entferne nicht verwendeten OAuth2 Controller und seine Argumente


## [0.1.90-alpha] - 2025-06-27

### Added
- feat: Updated routes configuration and removed OAuthClientController and OAut...


## [0.1.89-alpha] - 2025-06-27

### Added
- feat(Entity\Client): Update identifier column length and constructor parameters


## [0.1.88-alpha] - 2025-06-27

### Changed
- refactor: Remove redundant identifier property assignment in Client entity


## [0.1.87-alpha] - 2025-06-27

### Changed
- refactor: Removed unnecessary properties and assignments in Client entity


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

## [0.1.118-alpha] - 2025-06-28

- Commit: "Passwort-URL-Dekodierung für verbesserte Sicherheit hinzugefügt"...
- [M] composer.json
- [M] src/Infrastructure/Auth/Persistence/DoctrineUserRepository.php

## [0.1.119-alpha] - 2025-06-28

- Commit: "Korrigiere Leerzeichen in DoctrineUserRepository.php"...
- [M] composer.json
- [M] src/Infrastructure/Auth/Persistence/DoctrineUserRepository.php

## [0.1.120-alpha] - 2025-06-28

- Commit: Refaktorierung: Entfernung von Datumsformatierungen....
- [M] composer.json
- [M] src/Application/Auth/QueryHandler/GetActiveSessionsQueryHandler.php
- [M] src/Application/Auth/QueryHandler/GetOAuthClientsQueryHandler.php
- [M] src/Application/Auth/QueryHandler/GetUserProfileQueryHandler.php
- [M] src/Application/Auth/QueryHandler/ValidateTokenQueryHandler.php
- [M] src/Application/Plugin/Query/Handler/GetAllPluginsQueryHandler.php
- [M] src/Application/Plugin/Query/Handler/GetPluginQueryHandler.php
- [M] src/Application/Plugin/QueryHandler/GetActivePluginsQueryHandler.php
- [M] src/Application/Role/QueryHandler/GetAllRolesQueryHandler.php
- [M] src/Application/Role/QueryHandler/GetRoleQueryHandler.php
- [M] src/Application/Role/QueryHandler/GetUserRolesQueryHandler.php
- [M] src/Application/User/Query/Handler/GetUserQueryHandler.php
- [M] src/Domain/ValueObject/Email.php

## [0.1.121-alpha] - 2025-06-28

- Commit: "Entferne Doctrine- und Messenger-Konfiguration, aktualisiere Security-Einste...
- [D] Resources/config/doctrine.yaml
- [D] Resources/config/messenger.yaml
- [M] Resources/config/packages/security.yaml
- [D] Resources/config/security.yaml
- [M] composer.json
- [M] src/Application/User/Controller/UserController.php
- [M] src/Domain/Role/Domain/Entity/Role.php

## [0.1.122-alpha] - 2025-06-28

- Commit: "Entferne DocumentControllerExample für Hybrid Access Control"...
- [M] composer.json
- [D] src/Application/Document/Controller/DocumentControllerExample.php

## [0.1.123-alpha] - 2025-06-29

- Commit: "Entfernt veraltete Fixtures für Client, LeagueClient, Role und WebpackClient...
- [M] composer.json
- [D] src/Infrastructure/Role/Fixtures/ClientFixture.php
- [D] src/Infrastructure/Role/Fixtures/LeagueClientFixture.php
- [D] src/Infrastructure/Role/Fixtures/RoleFixture.php
- [D] src/Infrastructure/Role/Fixtures/WebpackClientFixture.php
- [M] src/Migrations/Version20250126160000.php

## [0.1.124-alpha] - 2025-06-29

- Commit: Hinzufügen von DataFixtures-Services für Doctrine-Fixtures in dev und test Um...
- [M] Resources/config/services.yaml
- [M] composer.json

## [0.1.125-alpha] - 2025-06-29

- Commit: Fix: Updated getDependencies method return type to array...
- [M] composer.json
- [M] src/DataFixtures/AgencyFixtures.php
- [M] src/DataFixtures/EcommerceFixtures.php
- [M] src/DataFixtures/FreelancerFixtures.php
- [M] src/DataFixtures/NeuroAIFixtures.php

## [0.1.126-alpha] - 2025-06-29

- Commit: "Fix: Fehler bei der Benutzeranmeldung behoben"...
- [M] composer.json

## [0.1.127-alpha] - 2025-06-29

- Commit: "Erweiterung der Benutzerrollen-Tabelle um neue Felder"...
- [M] composer.json
- [M] src/Migrations/Version20250101000000.php
- [M] src/Migrations/Version20250102000000.php
- [M] src/Migrations/Version20250126160000.php
- [M] src/Migrations/Version20250128210000.php

## [0.1.128-alpha] - 2025-06-29

- Commit: Entferne veraltete Migrationen für Plugins und Benutzer....
- [M] composer.json
- [D] src/Migrations/Version20250623213620.php

## [0.1.129-alpha] - 2025-06-29

- Commit: "Fix: Fehler beim Laden von Benutzerprofilen behoben"...
- [M] composer.json

## [0.1.130-alpha] - 2025-06-29

- Commit: "UUID-Generierung für NeuroAIFixtures hinzugefügt"...
- [M] composer.json
- [M] src/DataFixtures/NeuroAIFixtures.php

## [0.1.131-alpha] - 2025-06-29

- Commit: "UUIDs für Agenturen und Systeme generieren, Symfony-Rollen korrigieren"...
- [M] composer.json
- [M] src/DataFixtures/AgencyFixtures.php
- [M] src/DataFixtures/CoreSystemFixtures.php
- [M] src/DataFixtures/EcommerceFixtures.php
- [M] src/DataFixtures/FreelancerFixtures.php
- [M] src/Infrastructure/User/Security/UserProvider.php

## [0.1.132-alpha] - 2025-06-29

- Commit: Hinzufügen von PermissionService und Bereinigung der Authentifizierung....
- [M] composer.json
- [M] src/Application/User/Controller/UserController.php

## [0.1.133-alpha] - 2025-06-29

- Commit: "Optimierung der Berechtigungsüberprüfung und -abfrage in PermissionService"...
- [M] composer.json
- [M] src/Application/Role/Service/PermissionService.php

## [0.1.134-alpha] - 2025-06-29

- Commit: feat: Added ABAC rules and ACL entries for agency context....
- [M] composer.json
- [M] src/DataFixtures/AgencyFixtures.php
- [M] src/DataFixtures/CoreSystemFixtures.php
- [M] src/DataFixtures/EcommerceFixtures.php
- [M] src/DataFixtures/FreelancerFixtures.php
- [M] src/DataFixtures/NeuroAIFixtures.php

## [0.1.135-alpha] - 2025-06-29

- Commit: Überspringe Duplikat-Migration - Tabellen existieren bereits (Version20250101...
- [M] composer.json
- [M] src/DataFixtures/CoreSystemFixtures.php
- [M] src/Migrations/Version20250623231507.php

## [0.1.136-alpha] - 2025-06-29

- Commit: Skip duplicate migration - company_settings table already exists...
- [M] composer.json
- [M] src/Migrations/Version20250625130000.php

## [0.1.137-alpha] - 2025-06-29

- Commit: "Fehler behoben und Leistung verbessert"...
- [M] composer.json

## [0.1.138-alpha] - 2025-06-29

- Commit: "Implementiere ABAC-basierte Berechtigungsprüfung mit Kontextunterstützung"...
- [M] composer.json
- [M] src/Application/Role/Service/PermissionService.php
- [M] src/Application/User/Controller/UserPermissionController.php

## [0.1.139-alpha] - 2025-06-29

- Commit: Hinzufügen der Methode zur Abfrage von Navigationsberechtigungen....
- [M] composer.json
- [M] src/Application/User/Controller/UserPermissionController.php

## [0.1.140-alpha] - 2025-06-29

- Commit: Refactor UserPermissionController to use current authenticated user....
- [M] composer.json
- [M] src/Application/User/Controller/UserPermissionController.php
