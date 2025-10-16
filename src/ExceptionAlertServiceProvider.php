<?php

namespace Sambukhari\ExceptionAlert;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ExceptionAlertServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/exception-alert.php', 'exception-alert');
    }

    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/exception-alert.php' => config_path('exception-alert.php'),
        ], 'config');

        // Publish email view
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'exception-alert');

        // Auto-inject into Handler.php
        $this->injectHandlerCode();
    }

    protected function injectHandlerCode()
    {
        (new \Sambukhari\ExceptionAlert\ExceptionHandlerInjector())->inject();
    }
}
