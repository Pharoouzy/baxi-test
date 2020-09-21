<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerificationSuccessful extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 5;

    public $timeout = 120;

    public $user;

    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $url) {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject(config('app.name').' Verification Successful')->from(config('mail.from.address'), config('app.name'))->markdown('mail.auth.email_verification_successful');
    }
}
