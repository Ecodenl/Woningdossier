@component('mail::message')
@lang('cooperation/mail/account-associated-with-cooperation.salutation', [
    'first_name' => $associatedUser->first_name,
    'last_name' => $associatedUser->last_name
])
<br>
<?php
// the route to the homepage.
$hoomdossier_url = route('cooperation.home', ['cooperation' => $userCooperation]);
$hoomdossier_href = __('<a href=":hoomdossier_url" target="_blank">:hoomdossier_url</a>', compact('hoomdossier_url'));

// the url to the website of the cooperation itself.
$cooperation_href = __('<a href=":cooperation_url" target="_blank">:cooperation_url</a>', ['cooperation_url' => $userCooperation->website_url]);
// the name of the cooperation itself
$cooperation_name = $userCooperation->name;
// imploded names from all the cooperations the user is associated with
$cooperation_names = $cooperations->pluck('name')->implode(', ');

$hoomdossier_reset_url = route('cooperation.auth.password.request.index', ['cooperation' => $userCooperation]);
$hoomdossier_reset_href = __('<a href=":hoomdossier_reset_url" target="_blank">:hoomdossier_reset_url</a>', compact('hoomdossier_reset_url'))
?>

@lang('cooperation/mail/account-associated-with-cooperation.text', compact(
    'hoomdossier_href',
    'cooperation_name',
    'cooperation_href',
    'cooperation_names',
    'hoomdossier_reset_href'))
<br><br>
@lang('cooperation/mail/account-associated-with-cooperation.kind_regards', ['app_name' => config('app.name')])
@endcomponent