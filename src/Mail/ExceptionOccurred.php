<?php

namespace Sambukhari\ExceptionAlert\Mail;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
        return $this->subject('🚨 Laravel Exception: '.$this->exception->getMessage())
            ->view('exception-alert::email')
            ->with([
                'messageText' => $this->exception->getMessage(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'url' => request()->fullUrl(),
                'trace' => $this->exception->getTraceAsString(),
            ]);
    }
}
