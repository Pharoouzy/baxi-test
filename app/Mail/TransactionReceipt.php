<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransactionReceipt extends Mailable implements ShouldQueue
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
    public $data, $user, $type;


    public function __construct($data, $user, $type = 'electricity') {
        $this->data = $data;
        $this->user = $user;
        $user->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('Transaction Receipt')
            ->from(config('mail.from.address'), config('app.name'))
            ->markdown('mail.transaction_receipt');
    }
}
