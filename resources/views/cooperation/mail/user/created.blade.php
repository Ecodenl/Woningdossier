@lang('mail.account-created-by-cooperation.salutation', [
    'first_name' => $createdUser->first_name,
    'last_name' => $createdUser->last_name
])
<br><br>
<?php

    // the confirm route.
    $resetUrl = route('cooperation.auth.password.reset.show', ['token' => $token, 'cooperation' => $userCooperation, 'email' => encrypt($createdUser->account->email)]);
    $resetHref = '<a target="_blank" href="'.$resetUrl.'">'.$resetUrl.'</a>';

    // the route to the hoompage.
    $cooperationHoomdossierLink = route('cooperation.home', ['cooperation' => $userCooperation]);
    $cooperationHoomdossierHref = '<a target="_blank" href="'.$cooperationHoomdossierLink.'">http://'.$userCooperation->slug.'.'.config('app.domain').'</a>';

    // the route to the website of the cooperation itself.
    $cooperationWebsiteHref = '<a target="_blank" href="'.$userCooperation->website_url.'">'.$userCooperation->name.'</a>'

?>
@lang('mail.account-created-by-cooperation.text', [
    'hoomdossier_link' => $cooperationHoomdossierHref,
    'home_url' => config('app.url'),
    'confirm_url' => $resetHref,
    'cooperation_link' => $cooperationWebsiteHref
])
<br><br>
@lang('mail.account-created-by-cooperation.kind_regards', ['app_name' => config('app.name')])