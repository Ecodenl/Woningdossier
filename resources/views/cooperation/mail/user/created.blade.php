@lang('mail.user-created.why')
<br><br>
<?php
    // we only create a confirm token if the password is not set.
    // so if its present we will send the password reset link, else they know there password and we send them to the login page,
    if ($createdUser->confirm_token != null) {
        $url = route('cooperation.password.reset', ['token' => $createdUser->confirm_token, 'cooperation' => $userCooperation->slug]);
    } else {
        $url = route('cooperation.login', ['cooperation' => $userCooperation->slug]);
    }
?>
<a target="_blank" href="{{$url}}">@lang('mail.user-created.action')</a>
<br><br>
@lang('mail.user-created.signature', ['app_name' => config('app.name')])