@component('cooperation.mail.components.message')

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/reset-password.salutation', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/reset-password.why')
    @endcomponent

    @component('cooperation.mail.parts.centered-button', [
        'href' => route('cooperation.auth.password.reset', ['cooperation' => $userCooperation, 'token' => $token]),
        'width' => '200'
    ])
        @lang('cooperation/mail/reset-password.button')
    @endcomponent

    @component('cooperation.mail.components.text', ['style' => 'margin-bottom: 0px;'])
        @lang('cooperation/mail/reset-password.button-does-not-work')
        @include('cooperation.mail.parts.long-ahref', [
            'href' =>  route('cooperation.auth.password.reset', ['cooperation' => $userCooperation, 'token' => $token])
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/reset-password.not_requested')
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/reset-password.kind_regards', ['app_name' => config('app.name')])
    @endcomponent

@endcomponent