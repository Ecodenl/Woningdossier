@component('mail::message')

@lang('cooperation/mail/changed-email.salutation', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name
])
<br>
<?php

// the confirm route.
$changedEmailUrl = route('cooperation.recover-old-email.recover', ['cooperation' => $user->cooperation, 'token' => $account->old_email_token]);
$changedEmailHref = '<a target="_blank" href="'.$changedEmailUrl.'">'.$changedEmailUrl.'</a>';

// the route to the website of the cooperation itself.
$cooperationWebsiteHref = '<a target="_blank" href="'.$user->cooperation->website_url.'">'.$user->cooperation->name.'</a>'

?>

@lang('cooperation/mail/changed-email.text', [
    'home_url' => config('app.url'),
])

@component('mail::button', ['url' => $changedEmailUrl])
    @lang('cooperation/mail/changed-email.button')
@endcomponent

@lang('cooperation/mail/changed-email.button-does-not-work')
<br>
{!! $changedEmailHref !!}
<br>
<br>
@lang('cooperation/mail/changed-email.any-questions', [
    'cooperation_link' => $cooperationWebsiteHref
])
<br>
<br>
@lang('cooperation/mail/changed-email.kind_regards', ['app_name' => config('app.name')])

@endcomponent