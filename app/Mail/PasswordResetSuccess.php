<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class PasswordResetSuccess extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 5;

    public $timeout = 120;
    /**
     * @var
     */
    public $user;

    /**
     * PasswordResetSuccess constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return PasswordResetSuccess
     */
    public function build()
    {
        return $this->subject(config('app.name').' Password Reset Successfully')->from(config('mail.from.address'), config('app.name'))->markdown('mail.auth.password.reset_success');
    }
}
