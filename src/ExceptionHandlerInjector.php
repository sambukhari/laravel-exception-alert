<?php

namespace Sambukhari\ExceptionAlert;

use Illuminate\Support\Facades\Log;

class ExceptionHandlerInjector
{
    public function inject()
    {
        $handlerPath = app_path('Exceptions/Handler.php');

        if (!file_exists($handlerPath)) {
            Log::error("ExceptionAlert: Handler.php not found.");
            return;
        }

        $content = file_get_contents($handlerPath);

        // Prevent duplicate injection
        if (strpos($content, 'ExceptionAlertTrait') !== false) {
            Log::info("ExceptionAlert: Trait already exists in Handler.php");
            return;
        }

        // Add import
        if (strpos($content, 'use Sambukhari\ExceptionAlert\Traits\ExceptionAlertTrait;') === false) {
            $content = preg_replace(
                '/namespace App\\\\Exceptions;\s*/',
                "namespace App\Exceptions;\n\nuse Sambukhari\ExceptionAlert\Traits\ExceptionAlertTrait;\n",
                $content
            );
        }

        // Add trait inside class
        $content = preg_replace(
            '/class Handler extends ExceptionHandler\s*\{/',
            "class Handler extends ExceptionHandler\n{\n    use ExceptionAlertTrait;\n",
            $content
        );

        file_put_contents($handlerPath, $content);

        Log::info('ExceptionAlert: Trait injected successfully into Handler.php');
    }
}
