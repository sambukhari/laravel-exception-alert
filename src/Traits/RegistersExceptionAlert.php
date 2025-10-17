<?php

namespace Sambukhari\ExceptionAlert\Traits;

use Illuminate\Support\Facades\Mail;
use Sambukhari\ExceptionAlert\Mail\ExceptionOccurred;
use Throwable;

trait RegistersExceptionAlert
{
    /**
     * Register the exception alert callback inside any Handler.
     *
     * @return void
     */
    public function registerExceptionAlert()
    {
        if (!config('exception-alert.enabled')) {
            return;
        }

        $this->reportable(function (Throwable $e) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            if (!config("exception-alert.exceptions.$status", true)) {
                return;
            }

            try {
                Mail::to(config('exception-alert.to'))
                    ->send(new ExceptionOccurred($e));
            } catch (\Exception $ex) {
                \Log::error('Exception alert email failed: ' . $ex->getMessage());
            }
        });
    }
}
