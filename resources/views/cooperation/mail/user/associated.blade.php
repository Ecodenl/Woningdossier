@component('cooperation.mail.components.message')
    <?php
    // the route to the homepage.
    $hoomdossier_url = route('cooperation.home', ['cooperation' => $userCooperation]);
    $hoomdossier_href = View::make('cooperation.mail.parts.ahref', ['href' => $hoomdossier_url]);

    // the url to the website of the cooperation itself.
    $cooperation_href = View::make('cooperation.mail.parts.ahref', [
        'href' => is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:".$userCooperation->cooperation_email,
    ]);

    // the name of the cooperation itself
    $cooperation_name = $userCooperation->name;
    // imploded names from all the cooperations the user is associated with
    $cooperation_names = $cooperations->pluck('name')->implode(', ');

    $hoomdossier_reset_url = route('cooperation.auth.password.request.index', ['cooperation' => $userCooperation]);
    $hoomdossier_reset_href = View::make('cooperation.mail.parts.ahref', ['href' => $hoomdossier_reset_url]);
    ?>

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-associated-with-cooperation.salutation', [
            'first_name' => $associatedUser->first_name,
            'last_name' => $associatedUser->last_name
        ])
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-associated-with-cooperation.account-created', compact('hoomdossier_href'))
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-associated-with-cooperation.text', compact('cooperation_names','hoomdossier_reset_href'))
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-associated-with-cooperation.any-questions', compact('cooperation_href', 'cooperation_name'))
    @endcomponent

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-associated-with-cooperation.kind_regards', ['app_name' => config('app.name')])
    @endcomponent
@endcomponent