# CompanyOS Demo Fixtures 🎯

**Realistische Demo-Daten für verschiedene Use Cases von CompanyOS**

## 📋 Übersicht

Die neuen Fixtures bieten **vollständige Demo-Umgebungen** für vier innovative Anwendungsfälle:

### 🏢 **Agentursoftware** (`agency`)
- **PixelAgentur GmbH** - Digitale Kreativagentur aus Berlin
- **Rollen**: Creative Director, Account Manager, Designer, Developer, Project Manager, Agency Client
- **8 Demo-Benutzer** mit realistischen Namen und E-Mails
- **Plugins**: Adobe Creative Suite, Figma Integration, Time Tracking, Client Feedback
- **Webhooks**: Slack, Client Portal, Invoice System

### 👨‍💻 **Freelancersoftware** (`freelancer`)
- **Maria Gonzalez Webdesign** - Solo-Freelancerin aus Hamburg
- **Rollen**: Freelancer Owner, Freelancer Client, Accountant, Subcontractor
- **6 Demo-Benutzer** inklusive Kunden und Steuerberater
- **Plugins**: Invoice Generator Pro, Time Tracking, Tax Assistant, Contract Templates
- **Webhooks**: Banking, Tax Software, Client Notifications, Calendar

### 🛒 **Ecommercesystem** (`ecommerce`)
- **FashionStyle Shop** - Online-Modeshop aus München
- **Rollen**: Shop Owner, Shop Manager, Product Manager, Customer Service, Warehouse, Marketing, Accountant, Customer
- **10 Demo-Benutzer** vom Owner bis zu VIP-Kunden
- **Plugins**: PayPal, Stripe, DHL Shipping, Google Analytics, SEO, Newsletter, Reviews, Inventory
- **Webhooks**: ERP, Warehouse, Marketing Automation, Accounting, Support

### 🧠 **NeuroAI-Agentur** (`neuroai`)
- **NeuroAI Lab** - KI-Agentur für neurodivergente Freelancer/Unternehmer aus Berlin
- **Rollen**: AI Director, Neurodivergenz Coach, AI Engineer, Automation Specialist, Neurodivergent Entrepreneur
- **7 Demo-Benutzer** inklusive neurodivergente Klienten (ADHD, Autismus, Dyslexie)
- **Plugins**: n8n Workflow Engine, AI Assistant (Neurodivergent), Focus Time Manager, Sensory UI Adapter
- **Webhooks**: n8n Automation Hub, AI Training Pipeline, Crisis Support, Accessibility Monitor
- **Besonderheiten**: Barrierefreie UI, KI-Unterstützung, Routinen-Automatisierung, Krisenhilfe

## 🚀 Verwendung

### 1. **Core System laden** (Erforderlich)
```bash
php bin/console doctrine:fixtures:load --group=core --no-interaction
```
**Erstellt:**
- OAuth2 Clients (Backend API, Frontend SPA, Mobile App)
- System-Rollen (Super Admin, User)
- Admin-Benutzer (`admin@companyos.dev` / `CompanyOS2024!`)

### 2. **Use Case wählen**

**Für Agenturen:**
```bash
php bin/console doctrine:fixtures:load --group=agency --no-interaction
```

**Für Freelancer:**
```bash
php bin/console doctrine:fixtures:load --group=freelancer --no-interaction
```

**Für Online-Shops:**
```bash
php bin/console doctrine:fixtures:load --group=ecommerce --no-interaction
```

**Für NeuroAI-Agenturen:**
```bash
php bin/console doctrine:fixtures:load --group=neuroai --no-interaction
```

### 3. **Alles auf einmal laden**
```bash
php bin/console doctrine:fixtures:load --group=all --no-interaction
```

### 4. **Mehrere Use Cases kombinieren**
```bash
php bin/console doctrine:fixtures:load --group=core --group=agency --group=freelancer --no-interaction
```

## 👥 Demo-Benutzer

### 🏢 **Agentursoftware** - PixelAgentur
| E-Mail | Name | Rolle | Passwort |
|--------|------|-------|----------|
| `sarah.mueller@pixelagentur.de` | Sarah Müller | Creative Director | `PixelAgentur2024!` |
| `thomas.weber@pixelagentur.de` | Thomas Weber | Account Manager | `PixelAgentur2024!` |
| `lisa.schmidt@pixelagentur.de` | Lisa Schmidt | Designer | `PixelAgentur2024!` |
| `julia.richter@pixelagentur.de` | Julia Richter | Project Manager | `PixelAgentur2024!` |
| `kunde@tech-startup.com` | Michael Johnson | Agency Client | `PixelAgentur2024!` |

### 👨‍💻 **Freelancersoftware** - Maria Gonzalez Webdesign
| E-Mail | Name | Rolle | Passwort |
|--------|------|-------|----------|
| `info@maria-webdesign.de` | Maria Gonzalez | Freelancer Owner | `MariaDesign2024!` |
| `kontakt@startup-innovativ.com` | Tech Startup | Freelancer Client | `MariaDesign2024!` |
| `steuerberatung@kanzlei-schmidt.de` | Peter Schmidt | Freelancer Accountant | `MariaDesign2024!` |

### 🛒 **Ecommercesystem** - FashionStyle Shop
| E-Mail | Name | Rolle | Passwort |
|--------|------|-------|----------|
| `owner@fashionstyle-shop.de` | Lisa Fashionista | Shop Owner | `FashionStyle2024!` |
| `manager@fashionstyle-shop.de` | Michael Commerce | Shop Manager | `FashionStyle2024!` |
| `kunde1@gmail.com` | Emma Mustermann | Shop Customer | `FashionStyle2024!` |
| `vip.kunde@premium.de` | Victoria VIP | Shop Customer | `FashionStyle2024!` |

### 🧠 **NeuroAI-Agentur** - NeuroAI Lab
| E-Mail | Name | Rolle | Passwort |
|--------|------|-------|----------|
| `dr.jensen@neuro-ai-lab.de` | Dr. Alex Jensen | AI Director | `NeuroAI2024!` |
| `maya.coach@neuro-ai-lab.de` | Maya NeuroCoach | Neurodivergenz Coach | `NeuroAI2024!` |
| `adhd.freelancer@kreativ-chaos.de` | Leo ADHDCreative | Neurodivergent Entrepreneur | `NeuroAI2024!` |
| `autistic.developer@logic-systems.de` | Aria AutisticDev | Neurodivergent Entrepreneur | `NeuroAI2024!` |

## 🔑 System-Admin
**Für alle Use Cases verfügbar:**
- **E-Mail**: `admin@companyos.dev`
- **Passwort**: `CompanyOS2024!`
- **Rolle**: Super Administrator

## 🔧 Technische Details

### Fixture-Gruppen
- `core` - Basis-System (OAuth2, System-Rollen, Admin)
- `agency` - Agentur-Daten
- `freelancer` - Freelancer-Daten  
- `ecommerce` - E-Commerce-Daten
- `neuroai` - NeuroAI-Agentur-Daten
- `all` - Alle Gruppen zusammen
- `info` - Zeigt Hilfe-Informationen

### Dependencies
- **Agency/Freelancer/Ecommerce** hängen von `CoreSystemFixtures` ab
- Fixtures werden automatisch in der richtigen Reihenfolge geladen

### Datenbank-Reset
```bash
# Datenbank komplett zurücksetzen
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction

# Dann neue Fixtures laden
php bin/console doctrine:fixtures:load --group=all --no-interaction
```

## 🎨 Realistisches Demo-Environment

### Company Settings
Jeder Use Case hat **authentische Firmen-Einstellungen**:
- **Agentur**: Kreativstraße 42, Berlin - Deutsch/EUR
- **Freelancer**: Kreativweg 15, Hamburg - Deutsch/EUR  
- **E-Commerce**: Modestraße 88, München - Deutsch/EUR
- **NeuroAI**: Inklusionsstraße 42, Berlin - Deutsch/EUR (barrierefreie Anreden)

### Plugin-Ecosystem
**Branchen-spezifische Plugins** mit realistischen Features:
- **Design-Tools** (Adobe, Figma) für Agenturen
- **Finanz-Tools** (Rechnungen, Steuer) für Freelancer
- **Shop-Tools** (Payment, Versand, SEO) für E-Commerce
- **KI & Accessibility-Tools** (n8n, AI Assistant, Sensory Adapter) für NeuroAI

### Webhook-Integrationen
**Realistische Webhook-URLs** für typische Integrationen:
- Slack, Client Portals für Agenturen
- Banking, Steuer-Software für Freelancer
- ERP, Marketing-Automation für E-Commerce
- n8n Automation, KI-Training, Krisenhilfe für NeuroAI

## 🚀 Demo-Modus aktivieren

Nach dem Laden der Fixtures steht ein **vollständig funktionsfähiges Demo-System** zur Verfügung, das zeigt, wie CompanyOS in verschiedenen Branchen eingesetzt werden kann!

**Pro-Tipp**: Starte mit `core` und füge dann deinen bevorzugten Use Case hinzu, um die verschiedenen Einsatzgebiete zu erkunden! 🎯 