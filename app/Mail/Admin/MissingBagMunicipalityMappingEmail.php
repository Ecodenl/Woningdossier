<?php

namespace App\Mail\Admin;

use App\Helpers\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class MissingBagMunicipalityMappingEmail extends Mailable implements ShouldQueue
{
    use Queueable;

    public string $municipalityName;

    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     */
    public function __construct(string $municipalityName)
    {
        $this->onQueue(Queue::APP_EXTERNAL);
        $this->municipalityName = $municipalityName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this
            ->subject(__('cooperation/mail/admin/missing-bag-municipality-mapping.subject'))
            ->view('cooperation.mail.admin.missing-bag-municipality-mapping');
    }
}