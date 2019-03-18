@lang('mail.confirm-account.salutation', [
    'first_name' => $user->first_name,
     'last_name' => $user->last_name
])
<br><br>
<?php
    $confirmUrl = route('cooperation.confirm', ['cooperation' => $cooperation, 'u' => $user->email, 't' => $user->confirm_token]);
    $confirmHref = '<a target="_blank" href="'.$confirmUrl.'">'.$confirmUrl.'</a>';

    $cooperationHoomdossierLink = route('cooperation.home', ['cooperation' => $cooperation]);
    $cooperationHoomdossierHref = '<a target="_blank" href="'.$cooperationHoomdossierLink.'">http://'.$cooperation->slug.'.'.config('app.domain').'</a>';

    $cooperationWebsiteHref = '<a target="_blank" href="'.$cooperation->website_url.'">'.$cooperation->name.'</a>'

?>
@lang('mail.confirm-account.text', [
    'hoomdossier_link' => $cooperation->slug,
    'home_url' => config('app.url'),
    'confirm_url' => $confirmHref,
    'cooperation_link' => $cooperationWebsiteHref
])
<br><br>
@lang('mail.confirm-account.kind_regards', ['app_name' => config('app.name')])