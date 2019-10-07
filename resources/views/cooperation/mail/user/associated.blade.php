@lang('mail.account-associated-with-cooperation.salutation', [
    'first_name' => $associatedUser->first_name,
    'last_name' => $associatedUser->last_name
])
<br><br>
<?php
$cooperation = $userCooperation->name;
// the route to the homepage.
$hoomdossier_link = route('cooperation.home', ['cooperation' => $userCooperation]);
// the link to the website of the cooperation itself.
$cooperation_link = '<a target="_blank" href="'.$userCooperation->website_url.'">'.$userCooperation->name.'</a>';
?>
@lang('mail.account-associated-with-cooperation.text', compact('hoomdossier_link', 'cooperation', 'cooperation_link'))
<br><br>
@lang('mail.account-associated-with-cooperation.kind_regards', ['app_name' => config('app.name')])