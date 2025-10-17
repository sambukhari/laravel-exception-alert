<?php

namespace Sambukhari\ExceptionAlert\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ExceptionOccurred extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function build()
    {
        $subject = sprintf(
            '🚨 [%s] Exception: %s',
            config('app.name', 'Laravel'),
            class_basename($this->exception)
        );

        return $this->subject($subject)
            ->view('exception-alert::email')
            ->with([
                'exception' => $this->exception,
                'app' => config('app.name', 'Laravel'),
                'url' => request()->fullUrl() ?? config('app.url'),
                'env' => config('app.env'),
            ]);
    }
}
