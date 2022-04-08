@lang('cooperation/mail/confirm-account.salutation', [
     'first_name' => $user->first_name,
     'last_name' => $user->last_name
 ])
<?php
$cooperationHoomdossierHref = route('cooperation.home', ['cooperation' => $userCooperation]);

$cooperationWebsiteHref = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : $userCooperation->cooperation_email;
?>

@lang('cooperation/mail/confirm-account.text', [
    'hoomdossier_link' => $cooperationHoomdossierHref
])

@lang('cooperation/mail/confirm-account.button'): {!! $verifyUrl !!}

@lang('cooperation/mail/confirm-account.any-questions', ['cooperation_link' => $cooperationWebsiteHref])

@lang('cooperation/mail/confirm-account.kind_regards', ['app_name' => config('app.name')])