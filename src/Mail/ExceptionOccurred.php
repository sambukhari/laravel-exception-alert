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
        return $this->subject('🚨 Exception Alert: ' . get_class($this->exception))
                    ->view('exception-alert::email')
                    ->with([
                        'exception' => $this->exception,
                        'url' => config('app.url'),
                        'app' => config('app.name'),
                    ]);
    }
}
