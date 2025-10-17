<?php

namespace Sambukhari\ExceptionAlert;

use Illuminate\Support\ServiceProvider;
use Sambukhari\ExceptionAlert\ExceptionHandlerInjector;

class ExceptionAlertServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/exception-alert.php', 'exception-alert');
    }

    public function boot()
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../config/exception-alert.php' => config_path('exception-alert.php'),
        ], 'exception-alert-config');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'exception-alert');

        // Automatically inject our handler trait
        (new ExceptionHandlerInjector())->inject();
    }
}
