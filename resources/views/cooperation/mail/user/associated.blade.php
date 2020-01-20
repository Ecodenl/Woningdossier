@lang('cooperation/mail/account-associated-with-cooperation.salutation', [
    'first_name' => $associatedUser->first_name,
    'last_name' => $associatedUser->last_name
])
<br><br>
<?php
// the route to the homepage.
$hoomdossier_link = route('cooperation.home', ['cooperation' => $userCooperation]);

// the url to the website of the cooperation itself.
$cooperation_link = $userCooperation->website_url;
// the name of the cooperation itself
$cooperation_name = $userCooperation->name;
// imploded names from all the cooperations the user is associated with
$cooperation_names = $cooperations->pluck('name')->implode(', ');

$hoomdossier_reset_link = route('cooperation.auth.password.request.index', ['cooperation' => $userCooperation]);
?>
@lang('cooperation/mail/account-associated-with-cooperation.text', compact('hoomdossier_link', 'cooperation_name', 'cooperation_link', 'cooperation_names', 'hoomdossier_reset_link'))
<br><br>
@lang('cooperation/mail/account-associated-with-cooperation.kind_regards', ['app_name' => config('app.name')])