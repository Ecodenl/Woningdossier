@component('cooperation.mail.components.message')
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/user/notify-resident-participant-added.salutation', [
            'name' => $user->getFullName(),
         ])
    @endcomponent
    @php
        $cooperationWebsiteHref = View::make('cooperation.mail.parts.ahref', [
            'href' => is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:" . $userCooperation->cooperation_email,
        ]);
    @endphp
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/user/notify-resident-participant-added.text', [
            'name' => $coach->getFullName(),
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/user/notify-resident-participant-added.any-questions', [
            'cooperation_link' => $cooperationWebsiteHref
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/user/notify-resident-participant-added.kind_regards', ['app_name' => config('app.name')])
    @endcomponent
@endcomponent