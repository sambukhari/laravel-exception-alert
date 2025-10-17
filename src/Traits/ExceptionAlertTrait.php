<?php

namespace Sambukhari\ExceptionAlert\Traits;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Sambukhari\ExceptionAlert\Mail\ExceptionOccurred;

/**
 * ExceptionAlertTrait
 *
 * This trait safely registers an exception alert hook without overriding
 * the project's existing report() or render() methods.
 *
 * When used inside App\Exceptions\Handler, it attaches a reportable() callback
 * to send email alerts for uncaught exceptions.
 */
trait ExceptionAlertTrait
{
    /**
     * Register the exception alert hook via Laravel's reportable() method.
     * Must be called inside the Handler's register() method.
     *
     * Example:
     * public function register()
     * {
     *     $this->registerExceptionAlert();
     *     // other reportable logic...
     * }
     */
    public function registerExceptionAlert(): void
    {
        if (!method_exists($this, 'reportable')) {
            Log::warning('ExceptionAlert: reportable() method not found in Handler.');
            return;
        }

        $this->reportable(function (Throwable $exception) {
            if (!config('exception-alert.enabled', true)) {
                return;
            }

            try {
                $status = method_exists($exception, 'getStatusCode')
                    ? $exception->getStatusCode()
                    : 500;

                if (!config("exception-alert.exceptions.$status", true)) {
                    return;
                }

                // Send email alert (sync by default)
                Mail::to(config('exception-alert.to'))->send(
                    new ExceptionOccurred($exception)
                );

                Log::info('ExceptionAlert: Exception email sent for ' . get_class($exception));
            } catch (\Throwable $mailEx) {
                Log::error('ExceptionAlert: Failed to send exception email', [
                    'error' => $mailEx->getMessage(),
                ]);
            }
        });
    }
}
