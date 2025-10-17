<?php

namespace Sambukhari\ExceptionAlert\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class UninstallExceptionAlert extends Command
{
    protected $signature = 'exception-alert:uninstall {--restore= : path to backup to restore}';
    protected $description = 'Uninstall Exception Alert: remove trait injection or restore backup';

    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem();
    }

    public function handle()
    {
        $handlerPath = app_path('Exceptions/Handler.php');

        if (! $this->files->exists($handlerPath)) {
            $this->error("Handler.php not found at {$handlerPath}.");
            return 1;
        }

        $restore = $this->option('restore');

        if ($restore) {
            if (! $this->files->exists($restore)) {
                $this->error("Backup file {$restore} not found.");
                return 1;
            }
            $this->files->copy($restore, $handlerPath);
            $this->info("Restored Handler.php from {$restore}");
            return 0;
        }

        $contents = $this->files->get($handlerPath);

        // Remove marker and trait use
        $contents = str_replace('// exception-alert: injected by sambukhari/laravel-exception-alert' . PHP_EOL, '', $contents);
        $contents = str_replace('    use ExceptionAlertTrait;' . PHP_EOL, '', $contents);
        $contents = str_replace('use Sambukhari\\ExceptionAlert\\Traits\\ExceptionAlertTrait;' . PHP_EOL, '', $contents);

        $this->files->put($handlerPath, $contents);
        $this->info('Removed ExceptionAlert injection from Handler.php (manual cleanup).');

        return 0;
    }
}
