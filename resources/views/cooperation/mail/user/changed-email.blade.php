@lang('mail.changed-email.salutation', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name
])
<br><br>
<?php

// the confirm route.
$changedEmailRoute = route('cooperation.recover-old-email.recover', ['cooperation' => $user->cooperation, 'token' => $account->old_email_token]);
$changedEmailHref = '<a target="_blank" href="'.$changedEmailRoute.'">'.$changedEmailRoute.'</a>';

// the route to the website of the cooperation itself.
$cooperationWebsiteHref = '<a target="_blank" href="'.$user->cooperation->website_url.'">'.$user->cooperation->name.'</a>';

?>
@lang('mail.changed-email.text', [
    'home_url' => config('app.url'),
    'recover_old_email_url' => $changedEmailHref,
    'cooperation_link' => $cooperationWebsiteHref
])
<br><br>
@lang('mail.changed-email.kind_regards', ['app_name' => config('app.name')])