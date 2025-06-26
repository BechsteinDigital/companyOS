# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
und dieses Projekt folgt [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.37-alpha] - 2025-06-26

### Changed
- 
[1;33mrefactor: Update service paths in services.yaml for better organization[0m

refactor: Update service paths in services.yaml for better organization


## [0.1.36-alpha] - 2025-06-26

### Maintenance
- 
[1;33mchore: Aktualisiere Pfade für Domain, Application und Infrastructure-Ressourcen, erhöhe Version auf 0.1.35-alpha[0m

chore: Aktualisiere Pfade für Domain, Application und Infrastructure-Ressourcen, erhöhe Version auf 0.1.35-alpha


## [0.1.34-alpha] - 2025-06-26

### Added
- 
[0;36m[PROMPT][0m Commit-Message für CompanyOS Core Bundle:
[1;33m[0;34m[INFO][0m Generating intelligent commit message using OpenAI...
chore: Update Alpha-Version auf 0.1.33-alpha in README.md[0m

[0;36m[PROMPT][0m Ist diese Commit-Message in Ordnung? (y/n/e für edit):
[0;34m[INFO][0m Generating intelligent commit message using OpenAI...
chore: Update Alpha-Version auf 0.1.33-alpha in README.md


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
