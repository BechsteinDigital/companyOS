# CompanyOS Core Bundle - Routing Setup

## Routen im Hauptprojekt importieren

Um die API-Routen des CompanyOS Core Bundles zu verwenden, müssen diese im Hauptprojekt importiert werden.

### 1. Hauptprojekt: config/routes.yaml

Füge folgende Zeilen in die `config/routes.yaml` des Hauptprojekts hinzu:

```yaml
# CompanyOS Core Bundle Routes
company_os_core:
    resource: '@CompanyOSCoreBundle/Resources/config/routes.yaml'
```

### 2. Verfügbare API-Endpunkte

Nach dem Import sind folgende API-Endpunkte verfügbar:

- **Users API**: `/api/users/*`
- **Roles API**: `/api/roles/*`
- **Plugins API**: `/api/plugins/*`
- **Webhooks API**: `/api/webhooks/*`
- **Settings API**: `/api/settings/*`
- **Auth API**: `/api/oauth2/*`

### 3. Routen prüfen

Nach dem Import können die Routen mit folgendem Befehl geprüft werden:

```bash
bin/console debug:router
```

### 4. Cache leeren

Nach dem Hinzufügen der Routen den Cache leeren:

```bash
bin/console cache:clear
``` 