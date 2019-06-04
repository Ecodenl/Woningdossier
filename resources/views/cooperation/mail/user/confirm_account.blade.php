@lang('mail.confirm-account.salutation', [
    'first_name' => $user->first_name,
     'last_name' => $user->last_name
])
<br><br>
<?php
    $confirmUrl = route('cooperation.confirm', ['cooperation' => $userCooperation, 'u' => $user->email, 't' => $user->confirm_token]);
    $confirmHref = '<a target="_blank" href="'.$confirmUrl.'">'.$confirmUrl.'</a>';

    $cooperationHoomdossierLink = route('cooperation.home', ['cooperation' => $userCooperation]);
    $cooperationHoomdossierHref = '<a target="_blank" href="'.$cooperationHoomdossierLink.'">https://'.$userCooperation->slug.'.'.config('app.domain').'</a>';

    $cooperationWebsiteHref = '<a target="_blank" href="'.$userCooperation->website_url.'">'.$userCooperation->name.'</a>'

?>
@lang('mail.confirm-account.text', [
    'hoomdossier_link' => $cooperationHoomdossierHref,
    'home_url' => config('app.url'),
    'confirm_url' => $confirmHref,
    'cooperation_link' => $cooperationWebsiteHref
])
<br><br>
@lang('mail.confirm-account.kind_regards', ['app_name' => config('app.name')])