# Getting Started

`eamirgh/skyroom-laravel` is a production-ready Laravel package for integrating with the [Skyroom Web Service API](https://www.skyroom.online).

---

## Requirements

- **PHP**: `^8.2` (PHP 8.2, 8.3, or 8.4)
- **Laravel Framework**: `^10.0`, `^11.0`, `^12.0`, or `^13.0`
- **cURL / Guzzle**: `guzzlehttp/guzzle ^7.5`

---

## Installation

Install the package via Composer:

```bash
composer require eamirgh/skyroom-laravel
```

The package uses Laravel's package auto-discovery, so the `SkyroomServiceProvider` and `Skyroom` facade are registered automatically.

---

## Publishing Configuration

Publish the `skyroom.php` configuration file to your application's `config/` directory:

```bash
php artisan vendor:publish --tag="skyroom-config"
```

This will create `config/skyroom.php`.

---

## Environment Configuration

Open your `.env` file and add your Skyroom API credentials:

```dotenv
# Your 50-character API key from Skyroom dashboard
SKYROOM_API_KEY=apikey-71-819540-0f178abb0c712c4cfd5ae13e4c54687a

# Optional overrides (defaults shown)
SKYROOM_BASE_URL=https://www.skyroom.online/skyroom/api/
SKYROOM_TIMEOUT=30
SKYROOM_RETRY_TIMES=3
SKYROOM_RETRY_SLEEP=100
```

::: tip API Key Security
Never commit your raw 50-character Skyroom API key to version control. Always store it in `.env` or your secret manager.
:::

---

## Verifying Setup

You can quickly verify your connection in Laravel Tinker (`php artisan tinker`):

```php
// Check connection and list your services
$services = Skyroom::services()->all();
dump($services);
```
