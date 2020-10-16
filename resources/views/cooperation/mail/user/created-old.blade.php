@component('mail::message')
@lang('cooperation/mail/account-created.salutation', [
    'first_name' => $createdUser->first_name,
    'last_name' => $createdUser->last_name,
])
<br>
<?php
    $hoomdossier_url = route('cooperation.home', ['cooperation' => $userCooperation]);
    $hoomdossier_href = __('<a href=":hoomdossier_url" target="_blank">:hoomdossier_url</a>', compact('hoomdossier_url'));

    $confirm_url = route('cooperation.auth.password.reset.show', ['token' => $token, 'cooperation' => $userCooperation, 'email' => encrypt($createdUser->account->email)]);

    $href = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:".$userCooperation->cooperation_email;
    $cooperation_href = '<a target="_blank" href="'.$href.'">'.$userCooperation->name.'</a>';
?>

@lang('cooperation/mail/account-created.text', compact('hoomdossier_href'))
<br>
<br>
@lang('cooperation/mail/account-created.confirm')

@component('mail::button', ['url' => $confirm_url])
    @lang('cooperation/mail/account-created.button')
@endcomponent

@lang('cooperation/mail/account-created.button-does-not-work')
<br>
<a href="{!! $confirm_url !!}">{!! $confirm_url !!}</a>
<br>
<br>
@lang('cooperation/mail/account-created.any-questions', compact('cooperation_href'))
<br>
<br>
@lang('cooperation/mail/account-created.kind_regards', ['app_name' => config('app.name')])

@endcomponent
