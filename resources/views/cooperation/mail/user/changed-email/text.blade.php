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
@lang('cooperation/mail/changed-email.text', [
    'home_url' => config('app.url'),
])

<br>
<br>
<a href='{{$changedEmailUrl}}'>{{$changedEmailUrl}}</a>
<br>
<br>
<?php $href = is_null($user->cooperation->cooperation_email) ? $user->cooperation->website_url : "mailto:".$user->cooperation->cooperation_email; ?>
@lang('cooperation/mail/changed-email.any-questions', [
    'cooperation_link' => "<a href='${href}'>${href}</a>"
])


<br><br>
@lang('cooperation/mail/changed-email.kind_regards', ['app_name' => config('app.name')])