@lang('mail.account-created-by-cooperation.salutation', [
    'first_name' => $createdUser->first_name,
    'last_name' => $createdUser->last_name,
])
<br><br>
<?php
    $hoomdossier_link = route('cooperation.home', ['cooperation' => $userCooperation]);
    $confirm_url = route('cooperation.auth.password.reset.show', ['token' => $token, 'cooperation' => $userCooperation, 'email' => encrypt($createdUser->account->email)]);
    $cooperation_link = '<a target="_blank" href="'.$userCooperation->website_url.'">'.$userCooperation->name.'</a>';
?>

@lang('mail.account-created-by-cooperation.text', compact('hoomdossier_link', 'confirm_url', 'cooperation_link'))
<br><br>
@lang('mail.account-created-by-cooperation.kind_regards', ['app_name' => config('app.name')])