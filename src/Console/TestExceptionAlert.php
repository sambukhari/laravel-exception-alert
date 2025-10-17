<?php

namespace Sambukhari\ExceptionAlert\Console;

use Illuminate\Console\Command;
use Throwable;

class TestExceptionAlert extends Command
{
    protected $signature = 'exception-alert:test';
    protected $description = 'Send a test exception email via ExceptionAlert';

    public function handle()
    {
        try {
            throw new \Exception('ExceptionAlert test exception at ' . now()->toDateTimeString());
        } catch (Throwable $e) {
            // Use the Mailable to simulate the email send (respects mail config)
            \Mail::to(config('exception-alert.to'))->send(new \Sambukhari\ExceptionAlert\Mail\ExceptionOccurred($e));
            $this->info('Test exception email sent (check inbox and laravel.log).');
        }
        return 0;
    }
}
