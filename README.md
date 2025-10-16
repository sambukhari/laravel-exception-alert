# Laravel Exception Alert 🚨

A lightweight Laravel package that sends automatic exception alerts directly to your email.  
Perfect for developers who want to stay informed about critical exceptions without complex monitoring tools.

---

## ✨ Features

- Automatically emails exceptions to the developer.
- Configurable exception types (e.g., 404, 401, 500).
- Simple `.env` setup — just add your email.
- Main ON/OFF switch for alerting.
- Seamless integration — no manual editing in `Handler.php`.

---

## ⚙️ Installation

Install the package via Composer:

```bash
composer require sambukhari/laravel-exception-alert
```

After installation, publish the configuration file:

```bash
php artisan vendor:publish --tag=exception-alert-config
```

This will create the config file at:

```
config/exception-alert.php
```

---

## 🔧 Configuration

Add your developer email to the `.env` file:

```env
EXCEPTION_ALERT_EMAIL=developer@example.com
```

Then open `config/exception-alert.php` — all options are `true` by default:

```php
return [
    'enabled' => true, // Master switch

    'exceptions' => [
        404 => true,
        401 => false,
        403 => true,
        419 => true,
        429 => true,
        500 => true,
    ],
];
```

If you want to disable all alerts, simply set:

```php
'enabled' => false
```

---

## 🚀 How It Works

Once installed, the package automatically injects its logic into Laravel’s global exception handler.  
You don’t need to modify `app/Exceptions/Handler.php` — it’s handled automatically.

Whenever an exception occurs:
1. The package checks if alerts are enabled.
2. It verifies if that exception type (e.g., 404, 500) is marked as `true` in the config.
3. If allowed, an email is sent to the address from `.env` with full details including:
   - Project name & URL
   - Exception message
   - File and line number
   - Stack trace

---

## 📬 Example Email Format

**Subject:** `[Laravel Exception Alert] Error on myproject.com`

**Body:**

```
Project: myproject.com
Environment: production
Exception: Division by zero
File: /var/www/html/app/Http/Controllers/HomeController.php:42
```

---

## 🧑‍💻 Local Testing

You can trigger a test alert manually:

```bash
php artisan exception-alert:test
```

---

## 🪪 License

This package is open-source software licensed under the [MIT license](LICENSE).

---

## 👨‍💻 Author

**Syed Ali Mujtaba Shah (sambukhari)**  
[GitHub Profile](https://github.com/sambukhari)

---

## 💡 Contribution

Pull requests are welcome!  
If you find a bug or want to suggest improvements, feel free to open an issue.

