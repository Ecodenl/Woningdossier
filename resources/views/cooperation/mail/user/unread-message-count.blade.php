@component('cooperation.mail.components.message')

    <?php
    $cooperationHoomdossierLink = route('cooperation.auth.login', ['cooperation' => $userCooperation]);
    ?>

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/unread-message-count.salutation', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/unread-message-count.text', [
            'unread_message_count' => $unreadMessageCount,
        ])
    @endcomponent

    @component('cooperation.mail.parts.centered-button', ['href' => $cooperationHoomdossierLink])
        @lang('cooperation/mail/unread-message-count.button')
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/unread-message-count.button-does-not-work')
        @include('cooperation.mail.parts.ahref', ['href' => $cooperationHoomdossierLink])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/unread-message-count.if-you-have-any-questions', [
            'cooperation_link' =>  View::make('cooperation.mail.parts.ahref', [
                'href' => is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:".$userCooperation->cooperation_email,
            ])
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/unread-message-count.kind_regards', ['app_name' => config('app.name')])
    @endcomponent
@endcomponent