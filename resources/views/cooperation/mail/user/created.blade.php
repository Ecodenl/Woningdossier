@lang('mail.user-created.why')
<br><br>
<?php
    $url = route('cooperation.password.reset', ['token' => $token, 'cooperation' => $userCooperation->slug]);
?>
<a target="_blank" href="{{$url}}">@lang('mail.user-created.action')</a>
<br><br>
@lang('mail.user-created.signature', ['app_name' => config('app.name')])
