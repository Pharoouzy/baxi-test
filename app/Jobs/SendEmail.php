<?php

namespace App\Jobs;

use App\Mail\EmailVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $timeout = 120;

    protected $details;

    public $user;

    public $staff;

    public $url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $url, $staff = false) {
        $this->user = $user;
        $this->url = $url;
        $this->staff = $staff;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new EmailVerification($this->user, $this->url, $this->staff);
        Mail::to($this->user->email)->send($email);
    }
}
