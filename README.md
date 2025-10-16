<p align="center">
  <img src="https://laravel.com/img/logomark.min.svg" width="100" alt="Laravel Logo">
</p>

<h1 align="center">🚨 Laravel Exception Alert</h1>

<p align="center">
  <b>Instant Email Alerts for Laravel Exceptions — Simple, Fast & Developer-Friendly</b>
</p>

<p align="center">
  <a href="https://packagist.org/packages/sambukhari/laravel-exception-alert"><img src="https://img.shields.io/packagist/v/sambukhari/laravel-exception-alert.svg?style=flat-square" alt="Packagist Version"></a>
  <a href="https://packagist.org/packages/sambukhari/laravel-exception-alert"><img src="https://img.shields.io/packagist/dt/sambukhari/laravel-exception-alert.svg?style=flat-square" alt="Downloads"></a>
  <a href="https://github.com/sambukhari/laravel-exception-alert/stargazers"><img src="https://img.shields.io/github/stars/sambukhari/laravel-exception-alert?style=flat-square" alt="GitHub Stars"></a>
  <a href="https://github.com/sambukhari/laravel-exception-alert/blob/main/LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square" alt="License"></a>
</p>

---

## 📦 Introduction

**Laravel Exception Alert** automatically sends detailed exception alerts directly to your **email inbox** whenever a critical error occurs in your Laravel application.

✅ No manual setup  
✅ No third-party dependencies  
✅ 100% plug-and-play

---

## 🚀 Installation

Run the following command in your Laravel project:

```bash
composer require sambukhari/laravel-exception-alert
⚙️ Configuration

Publish the configuration file:

php artisan vendor:publish --provider="Sambukhari\\ExceptionAlert\\ExceptionAlertServiceProvider"


This creates a config file at:

config/exception-alert.php

Example Configuration
return [
    // Global ON/OFF switch for alerts
    'enabled' => true,

    // Developer email to receive exception alerts
    'developer_email' => env('EXCEPTION_ALERT_EMAIL', 'your@email.com'),

    // Control which exception types send alerts
    'exceptions' => [
        '404' => true,
        '401' => false,
        '419' => false,
        '500' => true,
        '400' => true,
        '403' => true,
    ],
];


Add your email to .env file:

EXCEPTION_ALERT_EMAIL=developer@yourdomain.com

🧠 How It Works

When an exception occurs:

The package automatically hooks into Laravel’s exception handler (App\Exceptions\Handler).

It checks:

If alerts are globally enabled

If the exception’s status code is allowed in config

Sends an email to the configured address containing:

Project name

URL

Exception class

Error message

Stack trace

📨 Example Email

Subject:

🚨 Exception in Your Laravel App [Production]


Body:

Project: EzeTech CRM
URL: https://ezecrm.com
Environment: production
Exception: Symfony\Component\HttpKernel\Exception\NotFoundHttpException
Message: Route [xyz] not found
File: /var/www/html/app/Http/Controllers/ExampleController.php:45

🧩 Auto Code Injection

When the package is installed, the ExceptionHandlerInjector automatically injects the necessary logic into your App\Exceptions\Handler.php file — no manual edits required!

If you uninstall the package, Laravel’s default exception behavior is restored automatically.

To remove:

composer remove sambukhari/laravel-exception-alert

🛠 Customization

You can customize email design or logic by publishing the views:

php artisan vendor:publish --tag=exception-alert-views

💡 Why Use This Package?

This is perfect for developers who:

Manage multiple Laravel apps

Want quick email notifications for errors

Prefer lightweight monitoring without Sentry or Bugsnag

Want fast debugging without external dashboards

👨‍💻 Author

Developed by: Syed Ali Mujtaba Shah (Sam Bukhari)

Role: Senior Software Engineer | Team Lead @ Eze Technologies

If you find this package useful — please ⭐ star the repo on GitHub!

🔮 Coming Soon

Telegram & Slack Alert Support

Mobile App for Real-Time Exception Alerts

Web Dashboard for Exception History

📜 License

This package is open-sourced software licensed under the MIT license
.

<p align="center"> <sub>Crafted with ❤️ by <a href="https://github.com/sambukhari">Sam Bukhari</a></sub> </p> ```