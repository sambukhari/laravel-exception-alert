# 🚨 Laravel Exception Alert

[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)]() [![Packagist Version](https://img.shields.io/packagist/v/sambukhari/laravel-exception-alert.svg?style=flat-square)]()  

A lightweight, safe and professional Laravel package that sends exception details to a developer email.  
Designed to be non-intrusive: it uses a trait, provides a **one-time installer** for Handler integration, publishes config and views, and includes safe uninstall/rollback.

---

## Key features

- Trait-based alerting to avoid method collisions with projects that already override `report()`.  
- One-time installer command `php artisan exception-alert:install` — **no runtime injection**.  
- Publishes config and email view for customization.  
- Test command to send a sample alert.  
- Uninstall/restore command to safely remove changes.  
- Idempotent and creates backups before modifying `app/Exceptions/Handler.php`.  

---

## Requirements

- PHP >= 8.0  
- Laravel 8 / 9 / 10 / 11 (or compatible)  
- Proper mail configuration in your `.env` (SMTP, Mailgun, SES, etc.)

---

## Installation (step-by-step)

1. **Require the package**

   If the package is on Packagist:
   ```bash
   composer require sambukhari/laravel-exception-alert
   ```

   If you are installing from your GitHub repository (recommended for testing before publishing):
   1. Add repository entry to your app `composer.json` (optional):
      ```json
      "repositories": [
        {
          "type": "vcs",
          "url": "https://github.com/sambukhari/laravel-exception-alert"
        }
      ]
      ```
   2. Then require the package:
      ```bash
      composer require sambukhari/laravel-exception-alert:dev-main --prefer-source
      ```

2. **Run the one-time installer**:
   ```bash
   php artisan exception-alert:install
   ```

   - Publishes `config/exception-alert.php` and `resources/views/vendor/exception-alert`.
   - Injects the trait into `app/Exceptions/Handler.php`.
   - Creates a timestamped backup.

3. **Set your recipient email** in `.env`:
   ```bash
   EXCEPTION_ALERT_EMAIL=developer@example.com
   EXCEPTION_ALERT_ENABLED=true
   ```

4. **Test email**:
   ```bash
   php artisan exception-alert:test
   ```

5. **Clear caches**:
   ```bash
   php artisan optimize:clear
   composer dump-autoload -o
   ```

---

## Configuration

```php
return [
    'enabled' => env('EXCEPTION_ALERT_ENABLED', true),
    'to' => env('EXCEPTION_ALERT_EMAIL', null),
    'exceptions' => [
        400 => true,
        404 => false,
        500 => true,
    ],
];
```

---

## Commands

| Command | Description |
|----------|--------------|
| `php artisan exception-alert:install` | Install and inject trait |
| `php artisan exception-alert:test` | Send test alert email |
| `php artisan exception-alert:uninstall` | Remove injected trait |

---

## Uninstall / Rollback

```bash
php artisan exception-alert:uninstall
composer remove sambukhari/laravel-exception-alert
```

---

## Contributing

1. Fork this repo  
2. Create feature branch  
3. Submit PR  

---

## License

This project is open-sourced under the [MIT License](LICENSE).
