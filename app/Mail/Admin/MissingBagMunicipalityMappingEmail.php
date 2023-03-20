<?php

namespace App\Mail\Admin;

use Illuminate\Mail\Mailable;

class MissingBagMunicipalityMappingEmail extends Mailable
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
            ->subject(__('cooperation/mail/admin/missing-bag-municipality-mapping.subject'))
            ->view('cooperation.mail.admin.missing-bag-municipality-mapping');
    }
}