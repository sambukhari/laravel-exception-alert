<?php

namespace Sambukhari\ExceptionAlert\Traits;

use Throwable;
use Illuminate\Support\Facades\Mail;
use Sambukhari\ExceptionAlert\Mail\ExceptionOccurred;

trait ExceptionAlertTrait
{
    public function report(Throwable $exception)
    {
        // Call original report method if exists
        if (is_callable(['parent', 'report'])) {
            parent::report($exception);
        }

        // Handle alert email
        if (config('exception-alert.enabled')) {
            $status = method_exists($exception, 'getStatusCode')
                ? $exception->getStatusCode()
                : 500;

            if (config("exception-alert.exceptions.$status", true)) {
                try {
                    Mail::to(config('exception-alert.to'))
                        ->send(new ExceptionOccurred($exception));
                } catch (\Exception $e) {
                    \Log::error('ExceptionAlert email failed: ' . $e->getMessage());
                }
            }
        }
    }
}
