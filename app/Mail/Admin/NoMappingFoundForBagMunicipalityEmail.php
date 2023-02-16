<?php

namespace App\Mail\Admin;

use Illuminate\Mail\Mailable;

class NoMappingFoundForBagMunicipalityEmail extends Mailable
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
            ->subject(__('cooperation/mail/admin/no-mapping-found-for-bag-municipality.subject'))
            ->view('cooperation.mail.admin.no-mapping-found-for-bag-municipality');
    }
}