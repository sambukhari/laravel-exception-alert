<?php

namespace Sambukhari\ExceptionAlert\Traits;

use Throwable;
use Illuminate\Support\Facades\Mail;
use Sambukhari\ExceptionAlert\Mail\ExceptionOccurred;

trait ExceptionAlertTrait
{
    /**
     * Extension-safe report method.
     * If the consuming class (Handler) defines its own report, it will call parent::report() by default.
     *
     * Note: When this trait is injected into Handler, it will add/override report().
     * We call parent::report($e) if available to preserve existing behavior.
     */
    public function report(Throwable $exception)
    {
        // Preserve existing report behavior (if parent exists)
        if (method_exists(get_parent_class($this) ?: '', 'report')) {
            try {
                parent::report($exception);
            } catch (\Throwable $ex) {
                // swallow parent report errors to avoid breaking alerts
                \Log::warning('ExceptionAlert: parent::report() threw: ' . $ex->getMessage());
            }
        } else {
            // call ExceptionHandler::report if available (safety)
            if (is_callable(['\Illuminate\Foundation\Exceptions\Handler', 'report'])) {
                try {
                    \Illuminate\Foundation\Exceptions\Handler::report($exception);
                } catch (\Throwable $ex) {
                    \Log::warning('ExceptionAlert: base report threw: ' . $ex->getMessage());
                }
            }
        }

        // Do our alerting
        try {
            if (!config('exception-alert.enabled', true)) {
                return;
            }

            $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;

            if (!config("exception-alert.exceptions.$status", true)) {
                return;
            }

            // Send email (synchronous). Users can change to queued by customizing Mailable.
            Mail::to(config('exception-alert.to'))->send(new ExceptionOccurred($exception));
        } catch (\Throwable $mailEx) {
            \Log::error('ExceptionAlert: Failed to send exception email: ' . $mailEx->getMessage());
        }
    }
}
