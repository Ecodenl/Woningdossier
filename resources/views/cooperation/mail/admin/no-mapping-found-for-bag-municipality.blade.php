@component('cooperation.mail.components.message')
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/admin/no-mapping-found-for-bag-municipality.body.text', ['name' => $municipalityName])
    @endcomponent
@endcomponent