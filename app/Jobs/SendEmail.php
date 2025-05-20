<?php

namespace App\Jobs;


use App\Mail\SendPasswordToAgents;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Queueable;

    protected $user_id;
    protected $password;

    /**
     * Create a new job instance.
     */
    public function __construct($user_id, $password)
    {
        $this->user_id = $user_id;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->user_id);
        Mail::to($user->email)->send(new SendPasswordToAgents($this->user_id, $this->password));
    }
}
