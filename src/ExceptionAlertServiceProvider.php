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
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/exception-alert.php' => config_path('exception-alert.php'),
        ], 'config');

        // Auto-register into Laravel's exception handler
        $this->registerExceptionHook();
    }

    /**
     * Automatically injects code into app/Exceptions/Handler.php.
     */
    protected function registerExceptionHook()
    {
        app()->resolving('Illuminate\Contracts\Debug\ExceptionHandler', function ($handler) {
            if (method_exists($handler, 'register') && method_exists($handler, 'reportable')) {
                // Dynamically inject the exception alert registration
                if (in_array(\Sambukhari\ExceptionAlert\Traits\RegistersExceptionAlert::class, class_uses_recursive($handler))) {
                    return;
                }

                // Mixin the trait dynamically
                $handlerClass = get_class($handler);
                $trait = \Sambukhari\ExceptionAlert\Traits\RegistersExceptionAlert::class;
                if (!in_array($trait, class_uses_recursive($handlerClass))) {
                    // Inject trait behavior at runtime
                    $handler->registerExceptionAlert = \Closure::fromCallable([new class {
                        use \Sambukhari\ExceptionAlert\Traits\RegistersExceptionAlert;
                    }, 'registerExceptionAlert']);

                    // Execute it
                    $handler->registerExceptionAlert();
                }
            }
        });
    }


}
