@lang('cooperation/mail/confirm-account.salutation', [
     'first_name' => $user->first_name,
     'last_name' => $user->last_name
 ])
<?php
$cooperationHoomdossierHref = route('cooperation.home', ['cooperation' => $userCooperation]);

$confirmHref = route('cooperation.auth.confirm.store', ['cooperation' => $userCooperation, 'u' => $user->account->email, 't' => $user->account->confirm_token]);

$cooperationWebsiteHref = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:" . $userCooperation->cooperation_email;
?>


@lang('cooperation/mail/confirm-account.text', [
    'hoomdossier_link' => "<a href='${cooperationHoomdossierHref}'>${cooperationHoomdossierHref}</a>"
])
<br>
<br>
<a href="{{$confirmHref}}">@lang('cooperation/mail/confirm-account.button')</a>
<br>
<br>
@lang('cooperation/mail/confirm-account.any-questions', ['cooperation_link' => "<a href='${cooperationWebsiteHref}'>${cooperationWebsiteHref}</a>"])
<br><br>
@lang('cooperation/mail/confirm-account.kind_regards', ['app_name' => config('app.name')])