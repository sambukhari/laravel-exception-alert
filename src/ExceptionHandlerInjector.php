<?php

namespace Sambukhari\ExceptionAlert;

use Illuminate\Support\Facades\File;

class ExceptionHandlerInjector
{
    protected string $handlerPath;
    protected string $marker;

    public function __construct()
    {
        $this->handlerPath = app_path('Exceptions/Handler.php');
        $this->marker = '// Sambukhari Exception Alert - injected';
    }

    /**
     * Inject email alert logic into Laravel's Handler.php file.
     */
    public function inject()
    {
        if (!File::exists($this->handlerPath)) {
            \Log::warning('ExceptionAlert: Handler.php not found, skipping injection.');
            return;
        }

        $content = File::get($this->handlerPath);

        // Avoid duplicate injection
        if (strpos($content, $this->marker) !== false) {
            return;
        }

        $injection = <<<PHP

    {$this->marker}
    use Illuminate\\Support\\Facades\\Mail;
    use Sambukhari\\ExceptionAlert\\Mail\\ExceptionOccurred;

    public function report(Throwable \$exception)
    {
        parent::report(\$exception);

        if (config('exception-alert.enabled')) {
            \$status = method_exists(\$exception, 'getStatusCode') ? \$exception->getStatusCode() : 500;

            if (config("exception-alert.exceptions.\$status", true)) {
                try {
                    Mail::to(config('exception-alert.to'))
                        ->send(new ExceptionOccurred(\$exception));
                } catch (\\Exception \$e) {
                    \Log::error('Exception alert email failed: ' . \$e->getMessage());
                }
            }
        }
    }

PHP;

        // Append our code at the end of the class
        $updated = preg_replace(
            '/}\s*$/',
            $injection . "\n}",
            $content
        );

        File::put($this->handlerPath, $updated);

        \Log::info('ExceptionAlert: Code injected successfully into Handler.php');
    }
}
