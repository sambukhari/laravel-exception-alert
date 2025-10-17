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

    public function handle(): int
    {
        $this->info('🚀 Installing Laravel Exception Alert...');

        $this->publishResources();
        $handlerPath = app_path('Exceptions/Handler.php');

        if (! $this->files->exists($handlerPath)) {
            $this->error("❌ Handler.php not found at {$handlerPath}. Please run this command inside a Laravel app.");
            return 1;
        }

        $contents = $this->files->get($handlerPath);
        $backupPath = $handlerPath . '.exception-alert.bak.' . time();

        // Check for previous injection
        $marker = '// exception-alert: injected by sambukhari/laravel-exception-alert';
        if (Str::contains($contents, $marker) && ! $this->option('force')) {
            $this->warn('⚠️  ExceptionAlert already integrated in Handler.php (marker found). Use --force to reinstall.');
            return 0;
        }

        // Make backup before editing
        $this->files->copy($handlerPath, $backupPath);
        $this->info("💾 Backup created at: {$backupPath}");

        // Step 1️⃣ Add trait import
        $contents = $this->injectTraitImport($contents);

        // Step 2️⃣ Add "use ExceptionAlertTrait;" inside the class
        $contents = $this->injectTraitUsage($contents, $marker);

        // Step 3️⃣ Add "$this->registerExceptionAlert();" inside register()
        $contents = $this->injectRegisterCall($contents);

        // Step 4️⃣ Write modified Handler
        $this->files->put($handlerPath, $contents);

        $this->newLine();
        $this->info('✅ ExceptionAlert successfully integrated into Handler.php!');
        $this->line('📩 Please set your EXCEPTION_ALERT_EMAIL in .env file.');
        $this->newLine();

        return 0;
    }

    /**
     * Publish config & view files
     */
    protected function publishResources(): void
    {
        $this->info('📦 Publishing configuration and views...');

        $this->callSilent('vendor:publish', [
            '--provider' => "Sambukhari\\ExceptionAlert\\ExceptionAlertServiceProvider",
            '--tag' => 'exception-alert-config',
        ]);

        $this->callSilent('vendor:publish', [
            '--provider' => "Sambukhari\\ExceptionAlert\\ExceptionAlertServiceProvider",
            '--tag' => 'exception-alert-views',
        ]);
    }

    /**
     * Inject the ExceptionAlertTrait import at the top.
     */
    protected function injectTraitImport(string $contents): string
    {
        $traitImport = 'use Sambukhari\\ExceptionAlert\\Traits\\ExceptionAlertTrait;';

        if (Str::contains($contents, $traitImport)) {
            $this->line('ℹ️  Trait import already present.');
            return $contents;
        }

        return preg_replace(
            '/(namespace\s+App\\\\Exceptions;)/',
            "$1\n\n{$traitImport}",
            $contents,
            1
        );
    }

    /**
     * Inject "use ExceptionAlertTrait;" inside Handler class.
     */
    protected function injectTraitUsage(string $contents, string $marker): string
    {
        if (Str::contains($contents, 'use ExceptionAlertTrait;')) {
            $this->line('ℹ️  Trait usage already found inside Handler.');
            return $contents;
        }

        if (preg_match('/class\s+Handler\s+extends\s+[^{]+\{/', $contents, $matches)) {
            $insertionPoint = strpos($contents, $matches[0]) + strlen($matches[0]);
            $injection = "\n    use ExceptionAlertTrait;\n    {$marker}\n";
            $contents = substr_replace($contents, $injection, $insertionPoint, 0);
            $this->info('✅ Injected "use ExceptionAlertTrait;" into Handler.');
        } else {
            $this->error('❌ Could not locate Handler class definition.');
        }

        return $contents;
    }

    /**
     * Ensure register() contains $this->registerExceptionAlert();
     */
    protected function injectRegisterCall(string $contents): string
    {
        if (Str::contains($contents, '$this->registerExceptionAlert();')) {
            $this->line('ℹ️  registerExceptionAlert() call already exists.');
            return $contents;
        }

        // If register() method exists, insert the call inside it
        if (preg_match('/function\s+register\s*\(\s*\)\s*\{/', $contents)) {
            $contents = preg_replace(
                '/(function\s+register\s*\(\s*\)\s*\{\s*)/',
                "$1\n        \$this->registerExceptionAlert();\n",
                $contents,
                1
            );
            $this->info('✅ Added $this->registerExceptionAlert(); inside register().');
        } else {
            // If no register() found, create one at the end of the class
            $contents = preg_replace(
                '/\}\s*$/',
                "\n    public function register()\n    {\n        \$this->registerExceptionAlert();\n    }\n\n}",
                $contents,
                1
            );
            $this->info('✅ Created register() method with $this->registerExceptionAlert();');
        }

        return $contents;
    }
}
