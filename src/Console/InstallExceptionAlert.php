<?php

namespace Sambukhari\ExceptionAlert\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InstallExceptionAlert extends Command
{
    protected $signature = 'exception-alert:install {--force : Overwrite Handler.php if necessary}';
    protected $description = 'Install Exception Alert: publish config & views and inject trait into app/Exceptions/Handler.php';

    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem();
    }

    public function handle()
    {
        $this->info('Publishing config and views...');
        // Publish programmatically (same as vendor:publish)
        $this->callSilent('vendor:publish', [
            '--provider' => "Sambukhari\\ExceptionAlert\\ExceptionAlertServiceProvider",
            '--tag' => 'exception-alert-config',
        ]);

        $this->callSilent('vendor:publish', [
            '--provider' => "Sambukhari\\ExceptionAlert\\ExceptionAlertServiceProvider",
            '--tag' => 'exception-alert-views',
        ]);

        $handlerPath = app_path('Exceptions/Handler.php');

        if (! $this->files->exists($handlerPath)) {
            $this->error("Handler.php not found at {$handlerPath}. Please run this command in a Laravel app.");
            return 1;
        }

        $contents = $this->files->get($handlerPath);

        // Idempotency checks
        $traitImport = 'use Sambukhari\\ExceptionAlert\\Traits\\ExceptionAlertTrait;';
        $traitUseLine = 'use ExceptionAlertTrait;';
        $marker = '// exception-alert: injected by sambukhari/laravel-exception-alert';

        if (Str::contains($contents, $marker)) {
            $this->info('ExceptionAlert already installed in Handler.php (marker found).');
            return 0;
        }

        // Insert trait import after namespace line
        $contents = preg_replace_callback('/^namespace\s+App\\\\Exceptions;\\s*/m', function ($m) use ($traitImport) {
            return $m[0] . PHP_EOL . $traitImport . PHP_EOL;
        }, $contents, 1, $count);

        if ($count === 0 && ! Str::contains($contents, $traitImport)) {
            // fallback: prepend import after <?php
            $contents = preg_replace('/^<\?php\\s*/', "<?php\n\nnamespace App\\Exceptions;\n\n{$traitImport}\n", $contents, 1);
        }

        // Insert trait usage into class body (after existing trait uses or after class line)
        if (preg_match('/class\s+Handler\s+extends\s+[^{]+\{/', $contents, $m)) {
            $classDeclaration = $m[0];
            $insertionPoint = strpos($contents, $classDeclaration) + strlen($classDeclaration);
            // place trait use immediately after class opening brace with newline
            $contents = substr_replace($contents, PHP_EOL . '    ' . $traitUseLine . PHP_EOL . '    ' . $marker . PHP_EOL, $insertionPoint, 0);
        } else {
            $this->error('Could not find Handler class declaration. Aborting injection.');
            return 1;
        }

        // Backup existing Handler.php
        $backupPath = $handlerPath . '.exception-alert.bak.' . time();
        $this->files->copy($handlerPath, $backupPath);
        $this->info("Backup created at: {$backupPath}");

        // Write modified Handler.php
        $this->files->put($handlerPath, $contents);
        $this->info('ExceptionAlert trait injected into Handler.php (one-time).');

        $this->info('Installation complete. Please verify config/exception-alert.php and set EXCEPTION_ALERT_EMAIL in your .env');
        return 0;
    }
}
