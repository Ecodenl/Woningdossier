@component('cooperation.mail.components.message')
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/admin/missing-bag-municipality-mapping.body.text', ['name' => $municipalityName])
    @endcomponent
@endcomponent