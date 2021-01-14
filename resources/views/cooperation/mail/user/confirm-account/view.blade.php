@component('cooperation.mail.components.message')
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/confirm-account.salutation', [
             'first_name' => $user->first_name,
             'last_name' => $user->last_name
         ])
    @endcomponent
    <?php
    $cooperationHoomdossierHref = View::make('cooperation.mail.parts.ahref', ['href' => route('cooperation.home', ['cooperation' => $userCooperation])]);

    $cooperationWebsiteHref = View::make('cooperation.mail.parts.ahref', [
        'href' => is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:" . $userCooperation->cooperation_email,
    ]);
    ?>
    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/confirm-account.text', [
            'hoomdossier_link' => $cooperationHoomdossierHref,
        ])
    @endcomponent


    @component('cooperation.mail.parts.centered-button', ['href' => $verifyUrl, 'width' => '200'])
        @lang('cooperation/mail/confirm-account.button')
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/changed-email.button-does-not-work')
        @include('cooperation.mail.parts.long-ahref', ['href' => $verifyUrl])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/confirm-account.any-questions', ['cooperation_link' => $cooperationWebsiteHref])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/confirm-account.kind_regards', ['app_name' => config('app.name')])
    @endcomponent
@endcomponent