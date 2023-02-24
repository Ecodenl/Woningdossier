<?php

namespace App\Mail\Admin;

use Illuminate\Mail\Mailable;

class MissingVbjehuisMunicipalityMappingEmail extends Mailable
{
    public string $municipalityName;
    /**
     * Create new message instance.
     *
     * UserCreatedEmail constructor.
     */
    public function __construct(string $municipalityName)
    {
        $this->municipalityName = $municipalityName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('cooperation/mail/admin/missing-vbjehuis-municipality-mapping.subject'))
            ->view('cooperation.mail.admin.missing-vbjehuis-municipality-mapping');
    }
}