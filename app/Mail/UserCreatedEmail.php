<?php

namespace App\Mail;

use App\Models\Cooperation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreatedEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $cooperation;


    /**
     * Create new message instance
     *
     * UserCreatedEmail constructor.
     * @param Cooperation $cooperation
     */
    public function __construct(Cooperation $cooperation)
    {
        $this->cooperation = $cooperation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // we cant send $cooperation, will be overwritten
        return $this->view('cooperation.mail.user.created')
            ->with('userCooperation', $this->cooperation);
    }
}
