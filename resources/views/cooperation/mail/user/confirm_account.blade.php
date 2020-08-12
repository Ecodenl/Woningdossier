@lang('cooperation/mail/confirm-account.salutation', [
    'first_name' => $user->first_name,
     'last_name' => $user->last_name
])
<br><br>
<?php
    $confirmUrl = route('cooperation.auth.confirm.store', ['cooperation' => $userCooperation, 'u' => $user->account->email, 't' => $user->account->confirm_token]);
    $confirmHref = '<a target="_blank" href="'.$confirmUrl.'">'.$confirmUrl.'</a>';

    $cooperationHoomdossierLink = route('cooperation.home', ['cooperation' => $userCooperation]);
    $cooperationHoomdossierHref = '<a target="_blank" href="'.$cooperationHoomdossierLink.'">https://'.$userCooperation->slug.'.'.config('app.domain').'</a>';

    $href = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:".$userCooperation->cooperation_email;
    $cooperationWebsiteHref = '<a target="_blank" href="'.$href.'">'.$userCooperation->name.'</a>'

?>
@lang('cooperation/mail/confirm-account.text', [
    'hoomdossier_link' => $cooperationHoomdossierHref,
    'home_url' => config('app.url'),
    'confirm_url' => $confirmHref,
    'cooperation_link' => $cooperationWebsiteHref
])
<br><br>
@lang('cooperation/mail/confirm-account.kind_regards', ['app_name' => config('app.name')])