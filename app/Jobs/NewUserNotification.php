<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\NewUserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NewUserNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('New user registered: ' . $this->user->email);

        Mail::to($this->user->email)->send(new NewUserCreated($this->user));
    }
}
