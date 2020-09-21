<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class CreateTransactionRequest
 * @package App\Mail
 */
class CreateTransactionRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var int
     */
    public $tries = 5;

    /**
     * @var int
     */
    public $timeout = 120;

    /**
     * @var
     */
    /**
     * @var
     */
    public $data, $user, $backoffice;


    /**
     * CreateTransactionRequest constructor.
     * @param $data
     * @param $user
     * @param bool $backoffice
     */
    public function __construct($data, $user, $backoffice = false) {
        $this->data = $data;
        $this->user = $user;
        $user->backoffice = $backoffice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('New Transaction Request')->from(config('mail.from.address'), config('app.name'))->markdown('mail.transaction_request');
    }
}
