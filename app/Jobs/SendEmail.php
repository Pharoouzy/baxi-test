<?php

namespace App\Jobs;

use App\Mail\EmailVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendEmail
 * @package App\Jobs
 */
class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    protected $details;

    /**
     * @var
     */
    /**
     * @var
     */
    public $user, $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $url) {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new EmailVerification($this->user, $this->url);
        Mail::to($this->user->email)->send($email);
    }
}
