# Gyr Falcon ERP

A complete, production-ready ERP platform with Odoo-style UI/UX, modular architecture, and enterprise-grade features.

## Features

### Core Platform
- **Modular Architecture**: Fully isolated modules in `/modules/{module_name}/` with install/uninstall SQL, routes, controllers, models, and views
- **Module Dependency Engine**: Automatic dependency resolution, prevents uninstall if required by other modules
- **RBAC (Role-Based Access Control)**: Admin, Manager, User, Viewer roles with JSON-based permissions
- **Audit Logging**: Complete action tracking with user, IP, timestamps
- **Notification System**: Real-time notifications with read/unread tracking

### UI/UX (Odoo-Style)
- **App Launcher**: Grid-style icon launcher with search
- **Top Navigation**: Company branding, user menu, notifications, language switch
- **Dynamic Sidebar**: Auto-updates based on active modules
- **View Types**: Kanban, List (Tree), Form, Dashboard views
- **Smart Buttons**: Context-aware action buttons
- **Responsive Design**: Mobile-friendly with 768px breakpoint

### Bilingual Support (EN/AR)
- Full English (LTR) and Arabic (RTL) support
- Instant language switching via API
- UI auto-direction switch
- 200+ translated strings

### Multi-Currency System
- Egyptian Pound (EGP), US Dollar (USD), Euro (EUR)
- Exchange rates with automatic conversion
- Currency formatting with symbols
- Per-company and per-transaction currency assignment

### Company Profile Management
- Company name, logo upload, address, contacts
- Tax ID, registration number
- Default currency and language preference
- Multi-company ready architecture

## Modules (16 Total)

| Module | Category | Description |
|--------|----------|-------------|
| CRM | Business Operations | Leads, opportunities, contacts, pipeline |
| Sales | Business Operations | Orders, invoices, customers, products |
| Purchase | Business Operations | Purchase orders, vendors, procurement |
| Accounting | Business Operations | Chart of accounts, journal entries, payments |
| HR | Business Operations | Employees, departments, attendance, payroll, leaves |
| Inventory | Business Operations | Warehouses, stock tracking, stock moves |
| Projects | Business Operations | Projects, tasks, timesheets, milestones |
| eCommerce | Retail & Trade | Online orders, product catalog |
| POS | Retail & Trade | Point of sale, sessions, receipts |
| Helpdesk | Business Operations | Support tickets, assignments, resolution |
| Fleet | Real Estate & Logistics | Vehicles, maintenance logs, drivers |
| IoT | Technology & Services | Devices, sensors, readings |
| Quality | Construction & Manufacturing | Quality checks, inspections, alerts |
| Maintenance | Construction & Manufacturing | Equipment, maintenance requests |
| Studio | Technology & Services | Custom fields, form builder |
| Prepress | Technology & Services | Job management, proofing, approvals |

## Tech Stack

- **Backend**: PHP 8.1+ (Custom MVC Framework)
- **Database**: MySQL 8.0 (utf8mb4)
- **Frontend**: Vanilla JS, CSS3 (no heavy frameworks)
- **Charts**: Chart.js 4.x
- **Icons**: Font Awesome 6.x

## Quick Start

### Prerequisites
- PHP 8.1+
- MySQL 8.0+
- Apache/Nginx or PHP built-in server

### Setup

```bash
# 1. Create database
mysql -u root -e "CREATE DATABASE gyr_falcon_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE USER 'gyrfalcon'@'localhost' IDENTIFIED BY 'GyrFalcon2024!';"
mysql -u root -e "GRANT ALL ON gyr_falcon_erp.* TO 'gyrfalcon'@'localhost';"

# 2. Run migrations
mysql -u gyrfalcon -p'GyrFalcon2024!' gyr_falcon_erp < database/migrations/001_core_schema.sql

# 3. Start development server
php -S 0.0.0.0:8080 -t public/

# 4. Access the application
# Open http://localhost:8080
# Login: admin@gyrfalcon.com / password
```

### Install Modules

Modules can be installed via the Module Manager UI at `/modules` or via API:

```bash
curl -X POST http://localhost:8080/api/modules/install \
  -H "Content-Type: application/json" \
  -d '{"module":"crm"}'
```

## Project Structure

```
Gyr_Falcon_ERP/
├── config/             # Application & database configuration
├── core/               # Core framework
│   ├── App.php         # Application singleton
│   ├── Controllers/    # Core controllers + API controllers
│   ├── auth/           # Authentication
│   ├── currency/       # Currency manager
│   ├── database/       # Database abstraction
│   ├── helpers/        # Global helper functions
│   ├── i18n/           # Translation system
│   ├── module_manager/ # Module loader & dependency engine
│   └── routing/        # Router
├── database/
│   └── migrations/     # SQL migrations
├── lang/               # Translation files (en.php, ar.php)
├── modules/            # All ERP modules
│   ├── {module}/
│   │   ├── module.json
│   │   ├── install.sql
│   │   ├── uninstall.sql
│   │   ├── routes.php
│   │   ├── controllers/
│   │   ├── models/
│   │   ├── views/
│   │   └── api/
├── public/             # Web root
│   ├── index.php       # Entry point
│   ├── css/app.css
│   └── js/app.js
├── storage/            # File uploads
└── views/              # View templates
    ├── layouts/        # Main layout
    ├── auth/           # Login page
    ├── dashboard/      # Dashboard
    ├── settings/       # Settings
    ├── users/          # User management
    ├── module_manager/ # Module store
    ├── audit/          # Audit logs
    ├── components/     # Reusable view components
    └── errors/         # Error pages
```

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/sidebar` | GET | Get dynamic sidebar menu |
| `/api/app-launcher` | GET | Get app launcher apps |
| `/api/modules` | GET | List all modules |
| `/api/modules/install` | POST | Install a module |
| `/api/modules/uninstall` | POST | Uninstall a module |
| `/api/lang/switch` | POST | Switch language |
| `/api/currencies` | GET | List currencies |
| `/api/currencies/convert` | POST | Convert currency |
| `/api/notifications` | GET | Get notifications |
| `/api/notifications/read` | POST | Mark notification read |
| `/api/dashboard/stats` | GET | Dashboard statistics |
| `/api/dashboard/charts` | GET | Chart data |

## License

Proprietary - Gyr Falcon ERP
