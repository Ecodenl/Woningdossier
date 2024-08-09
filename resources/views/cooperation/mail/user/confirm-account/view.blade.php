@component('cooperation.mail.components.message')
    @php
        $cooperationHoomdossierHref = View::make('cooperation.mail.parts.ahref', ['href' => route('cooperation.home', ['cooperation' => $userCooperation])]);

        $cooperationWebsiteHref = View::make('cooperation.mail.parts.ahref', [
            'href' => is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:" . $userCooperation->cooperation_email,
        ]);

        // Manually create the @component stuff so we can easily use it as replace.
        $verifyLink = View::make('cooperation.mail.parts.long-ahref', ['href' => $verifyUrl])->render();

        $verifyLinkContent = View::make('cooperation.mail.parts.centered-button', [
            'href' => $verifyUrl, 'width' => '200', 'slot' => __('cooperation/mail/confirm-account.button')
        ])->render() . View::make('cooperation.mail.components.text', [
            'slot' => __('cooperation/mail/changed-email.button-does-not-work') . $verifyLink
        ])->render();

        $customMail = \App\Helpers\Models\CooperationSettingHelper::getSettingValue($userCooperation, \App\Helpers\Models\CooperationSettingHelper::SHORT_VERIFICATION_EMAIL_TEXT);

        if (! is_null($customMail)) {
            // Split mail on the verify link part so we can neatly wrap it in text components.
            $mailParts = explode(':verify_link', $customMail, 2);

            // We can always expect 2 parts since the link MUST be included, and even if it's at the start,
            // it splits to 2 elements.
            $customMail = View::make('cooperation.mail.components.text', [
                'slot' => nl2br(trim($mailParts[0]))
            ])->render() . ':verify_link' . View::make('cooperation.mail.components.text', [
                'slot' => nl2br(trim($mailParts[1]))
            ])->render();

            $customMail = \App\Helpers\Str::of($customMail)->replace(':first_name', $user->first_name)
                ->replace(':last_name', $user->last_name)
                ->replace(':hoomdossier_link', $cooperationHoomdossierHref)
                ->replace(':verify_link', $verifyLinkContent)
                ->replace('cooperation_link', $cooperationWebsiteHref)
                ->__toString();
        }
    @endphp

    @if(! empty($customMail))
        {!! $customMail !!}
    @else
        @component('cooperation.mail.components.text')
            @lang('cooperation/mail/confirm-account.salutation', [
                 'first_name' => $user->first_name,
                 'last_name' => $user->last_name
             ])
        @endcomponent

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
    @endif
@endcomponent