@lang('mail.account-created-by-cooperation.salutation', [
    'first_name' => $createdUser->first_name,
    'last_name' => $createdUser->last_name
])
<br><br>
<?php

    // the confirm route.
    $confirmUrl = route('cooperation.confirm', ['cooperation' => $cooperation, 'u' => $createdUser->email, 't' => $createdUser->confirm_token]);
    $confirmHref = '<a target="_blank" href="'.$confirmUrl.'">'.$confirmUrl.'</a>';

    // the route to the hoompage.
    $cooperationHoomdossierLink = route('cooperation.home', ['cooperation' => $cooperation]);
    $cooperationHoomdossierHref = '<a target="_blank" href="'.$cooperationHoomdossierLink.'">http://'.$cooperation->slug.'.'.config('app.domain').'</a>';

    // the route to the website of the cooperation itself.
    $cooperationWebsiteHref = '<a target="_blank" href="'.$cooperation->website_url.'">'.$cooperation->name.'</a>'

?>
@lang('mail.account-created-by-cooperation.text', [
    'hoomdossier_link' => $cooperationHoomdossierHref,
    'home_url' => config('app.url'),
    'confirm_url' => $confirmHref,
    'cooperation_link' => $cooperationWebsiteHref
])
<br><br>
@lang('mail.account-created-by-cooperation.kind_regards', ['app_name' => config('app.name')])