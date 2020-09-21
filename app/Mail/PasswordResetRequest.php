<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class PasswordResetRequest
 * @package App\Mail
 */
class PasswordResetRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $tries = 5;

    public $timeout = 120;

    public $data;

    public $url;

    /**
     * PasswordResetRequest constructor.
     * @param $data
     */
    public function __construct($data, $url)
    {
        $this->data = $data;
        $this->url = $url;
    }

    /**
     * @return PasswordResetRequest
     */
    public function build()
    {
        // $url = config('app.main_url').'/password/reset?email='.$this->data['user']['email'].'&token='.$this->data['token'];

        return $this->subject(config('app.name').' Password Reset')->from(config('mail.from.address'), config('app.name'))->markdown('mail.auth.password.reset_request');
    }

}
