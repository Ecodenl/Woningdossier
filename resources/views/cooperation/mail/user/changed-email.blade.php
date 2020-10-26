@component('cooperation.mail.components.message')

    <?php
        // the confirm route.
        $changedEmailUrl = route('cooperation.recover-old-email.recover', ['cooperation' => $user->cooperation, 'token' => $account->old_email_token]);
    ?>

    @lang('cooperation/mail/changed-email.salutation', [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name
    ])
    <br>
    <br>
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/changed-email.text', [
            'home_url' => config('app.url'),
        ])
    @endcomponent

    @component('cooperation.mail.parts.centered-button', ['href' => $changedEmailUrl, 'width' => '200'])
        @lang('cooperation/mail/changed-email.button')
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/changed-email.button-does-not-work')
        @include('cooperation.mail.parts.long-ahref', ['href' => $changedEmailUrl])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/changed-email.any-questions', [
            'cooperation_link' => View::make('cooperation.mail.parts.ahref', [
                'href' => is_null($user->cooperation->cooperation_email) ? $user->cooperation->website_url : "mailto:".$user->cooperation->cooperation_email,
            ])
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/changed-email.kind_regards', ['app_name' => config('app.name')])
    @endcomponent

@endcomponent