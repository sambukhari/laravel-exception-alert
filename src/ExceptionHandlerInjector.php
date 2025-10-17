<?php

namespace Sambukhari\ExceptionAlert;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ExceptionHandlerInjector
{
    public function inject()
    {
        $handlerPath = app_path('Exceptions/Handler.php');

        if (!File::exists($handlerPath)) {
            Log::error('ExceptionAlert: Handler.php not found.');
            return;
        }

        $contents = File::get($handlerPath);

        // Prevent double injection
        if (strpos($contents, 'Sambukhari Exception Alert - injected') !== false) {
            Log::info('ExceptionAlert: Code already injected.');
            return;
        }

        // --- Step 1: Ensure proper imports (insert after namespace) ---
        if (preg_match('/namespace\s+App\\\\Exceptions;/', $contents, $matches)) {
            $importBlock = "\n\nuse Illuminate\\Support\\Facades\\Mail;\nuse Sambukhari\\ExceptionAlert\\Mail\\ExceptionOccurred;\n";
            $contents = preg_replace(
                '/(namespace\s+App\\\\Exceptions;)/',
                '$1' . $importBlock,
                $contents,
                1
            );
        }

        // --- Step 2: Add report() method safely ---
        $injectedCode = <<<EOT

    // Sambukhari Exception Alert - injected
    public function report(Throwable \$exception)
    {
        parent::report(\$exception);

        if (config('exception-alert.enabled')) {
            \$status = method_exists(\$exception, 'getStatusCode') ? \$exception->getStatusCode() : 500;

            if (config("exception-alert.exceptions.\$status", true)) {
                try {
                    \Mail::to(config('exception-alert.to'))
                        ->send(new \Sambukhari\ExceptionAlert\Mail\ExceptionOccurred(\$exception));
                } catch (\Exception \$e) {
                    \Log::error('Exception alert email failed: ' . \$e->getMessage());
                }
            }
        }
    }

EOT;

        // Insert before the last closing brace of the Handler class
        $contents = preg_replace('/}\s*$/', $injectedCode . "}\n", $contents);

        File::put($handlerPath, $contents);

        Log::info('ExceptionAlert: Code injected successfully into Handler.php');
    }
}
