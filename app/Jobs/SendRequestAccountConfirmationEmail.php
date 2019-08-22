<?php

namespace App\Jobs;

use App\Mail\RequestAccountConfirmationEmail;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRequestAccountConfirmationEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Cooperation
     */
    protected $cooperation;

    /**
     * Create a new job instance.
     *
     * @param User        $user
     * @param Cooperation $cooperation
     *
     * @return void
     */
    public function __construct(User $user, Cooperation $cooperation)
    {
        $this->cooperation = $cooperation;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // This queued job will now queue the sending of an e-mail
        Mail::to($this->user->account->email)->queue(new RequestAccountConfirmationEmail($this->user, $this->cooperation));
    }
}
