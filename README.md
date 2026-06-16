# ⚙️ Automations — Laravel ERP Module

[![Latest Version](https://img.shields.io/packagist/v/dev-3bdulrahman/automations.svg?style=flat-square)](https://packagist.org/packages/dev-3bdulrahman/automations)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue?style=flat-square)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11%2B%20%7C%2012%2B-red?style=flat-square)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)

A complete **Automations** module for Laravel ERP systems. Build automated workflows, manage webhook endpoints, handle integrations with external services, and stream events — with full API and Livewire admin interface.

---

## Features

- Workflow Builder (triggers, actions, conditions)
- Workflow Execution & Logging
- Webhook Endpoints & Delivery Logs
- Third-party Integration Management
- Integration Credentials (secure storage)
- Event-driven Automation
- REST API endpoints
- Arabic & English translations

## Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.2 \| ^8.3 |
| Laravel | ^11.0 \| ^12.0 |

## Installation

```bash
composer require dev-3bdulrahman/automations
```

Publish and run migrations:

```bash
php artisan vendor:publish --provider="Dev3bdulrahman\Automations\Providers\AutomationServiceProvider"
php artisan migrate
```

## Service Provider

Auto-discovered via Laravel package discovery. Manual registration in `bootstrap/providers.php`:

```php
Dev3bdulrahman\Automations\Providers\AutomationServiceProvider::class,
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release history.

## License

MIT License © [Abdulrahman](https://3bdulrahman.com)
