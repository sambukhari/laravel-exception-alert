<?php

namespace Sambukhari\ExceptionAlert;

use Illuminate\Support\ServiceProvider;

class ExceptionAlertServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        // Merge default config into user's config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/exception-alert.php',
            'exception-alert'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // ✅ Publish the configuration file
        $this->publishes([
            __DIR__ . '/../config/exception-alert.php' => config_path('exception-alert.php'),
        ], 'exception-alert-config');

        // ✅ Load package views (for email templates)
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'exception-alert');

        // ✅ Automatically inject exception handling code
        $this->autoInjectHandler();
    }

    /**
     * Automatically injects code into app/Exceptions/Handler.php.
     */
    protected function autoInjectHandler()
    {
        try {
            (new \Sambukhari\ExceptionAlert\ExceptionHandlerInjector())->inject();
        } catch (\Throwable $e) {
            \Log::error('Exception Alert: Failed to inject Handler code — ' . $e->getMessage());
        }
    }
}
