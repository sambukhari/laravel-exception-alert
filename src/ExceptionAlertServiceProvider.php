<?php

namespace Sambukhari\ExceptionAlert;

use Illuminate\Support\ServiceProvider;

class ExceptionAlertServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge package config so config(...) works without publishing
        $this->mergeConfigFrom(__DIR__ . '/../config/exception-alert.php', 'exception-alert');
    }

    public function boot()
    {
        // Publishable resources (only registered - publishing happens via command or vendor:publish)
        $this->publishes([
            __DIR__ . '/../config/exception-alert.php' => config_path('exception-alert.php'),
        ], 'exception-alert-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/exception-alert'),
        ], 'exception-alert-views');

        // Load default view namespace
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'exception-alert');

        // Register console commands (installer/uninstaller/test)
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallExceptionAlert::class,
                Console\UninstallExceptionAlert::class,
                Console\TestExceptionAlert::class,
            ]);
        }
    }
}
