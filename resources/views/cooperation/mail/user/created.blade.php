@component('cooperation.mail.components.message')

    <?php
        $hoomdossier_url = route('cooperation.home', ['cooperation' => $userCooperation]);

        $confirmUrl = route('cooperation.auth.password.reset.show', ['token' => $token, 'cooperation' => $userCooperation, 'email' => encrypt($createdUser->account->email)]);

        $href = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:".$userCooperation->cooperation_email;
        $cooperation_href = '<a target="_blank" href="'.$href.'">'.$userCooperation->name.'</a>';
    ?>

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-created.salutation', [
            'first_name' => $createdUser->first_name,
            'last_name' => $createdUser->last_name,
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        {!! __('cooperation/mail/account-created.text', ['hoomdossier_href' => View::make('cooperation.mail.parts.ahref', ['href' => $hoomdossier_url])->render()]) !!}
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-created.confirm')
    @endcomponent

    @component('cooperation.mail.parts.centered-button', ['href' => $confirmUrl])
        @lang('cooperation/mail/account-created.button')
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-created.button-does-not-work')
    @endcomponent

    @include('cooperation.mail.parts.long-ahref', ['href' => $confirmUrl])

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-created.any-questions', compact('cooperation_href'))
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-created.kind_regards', ['app_name' => config('app.name')])
    @endcomponent
@endcomponent