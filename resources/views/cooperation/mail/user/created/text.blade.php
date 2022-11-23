<?php
$hoomdossier_url = route('cooperation.home', ['cooperation' => $userCooperation]);

$confirmUrl = route('cooperation.auth.password.reset', ['token' => $token, 'cooperation' => $userCooperation]);

$cooperationHref = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:" . $userCooperation->cooperation_email;

$cooperationName = $userCooperation->name
?>

@lang('cooperation/mail/account-created.salutation', [
    'first_name' => $createdUser->first_name,
    'last_name' => $createdUser->last_name,
])

<br><br>
{!! __('cooperation/mail/account-created.text', ['hoomdossier_href' => "<a href='${hoomdossier_url}'>${hoomdossier_url}</a>"]) !!}
<br><br>
@lang('cooperation/mail/account-created.confirm')
<br><br>

<a href="{{$confirmUrl}}">@lang('cooperation/mail/account-created.button')</a>
<br><br>
@lang('cooperation/mail/account-created.any-questions', ['cooperation_href' => "<a href='${cooperationHref}'>${cooperationName}</a>"])
<br><br>
@lang('cooperation/mail/account-created.kind_regards', ['app_name' => config('app.name')])