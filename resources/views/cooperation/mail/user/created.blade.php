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

{{--    @component('cooperation.mail.components.text')--}}
        @include('cooperation.mail.parts.long-ahref', ['href' => $confirmUrl])
{{--        <a href="http://hoom.woondossier.vm/password/reset/7dc7d4a59eb923606129182d2beaaa361ad696dac8fb77de988a03ca2e7874c4/eyJpdiI6Im5BaklDNVhKMkpqKytERVV6bmk2WkE9PSIsInZhbHVlIjoiTjhFWHpRMkJjZnEzYkNpd1wvU3Yrd0dLS0lnQndjdUZwNEV5aWtTa0Q2bk09IiwibWFjIjoiMDBjOWIyOTEyY2NkYmJlYzYxNWI2ODQzZDc1MTg2NGIwNWU1Njk3MzY3Njc4ZDA3ZWNmZjc5YmM1NDFlMmIwMyJ9" style="word-break:break-all; display: block !important; max-width: 570px; font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #3869D4;">--}}
{{--            http://hoom.woondossier.vm/password/reset/7dc7d4a59eb923606129182d2beaaa361ad696dac8fb77de988a03ca2e7874c4/eyJpdiI6Im5BaklDNVhKMkpqKytERVV6bmk2WkE9PSIsInZhbHVlIjoiTjhFWHpRMkJjZnEzYkNpd1wvU3Yrd0dLS0lnQndjdUZwNEV5aWtTa0Q2bk09IiwibWFjIjoiMDBjOWIyOTEyY2NkYmJlYzYxNWI2ODQzZDc1MTg2NGIwNWU1Njk3MzY3Njc4ZDA3ZWNmZjc5YmM1NDFlMmIwMyJ9--}}
{{--        </a>--}}
{{--    @endcomponent--}}

    @component('cooperation.mail.components.text')
        @lang('cooperation/mail/account-created.any-questions', compact('cooperation_href'))
    @endcomponent


@endcomponent