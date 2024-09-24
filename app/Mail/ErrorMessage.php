<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ErrorMessage extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * The user instance.
     *
     * @var User
     */
    public $user;
    
    /**
     * The name of the message.
     *
     * @var string
     */
    public $messageName;
    
    /**
     * The exception instance.
     *
     * @var Throwable
     */
    public $exception;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $messageName, \Throwable $exception)
    {
        $this->user = $user;
        $this->messageName = $messageName;
        $this->exception = $exception;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.error-message')
                    ->with([
                        'user' => $this->user,
                        'messageName' => $this->messageName,
                        'exception' => $this->exception
                    ]);
    }
}