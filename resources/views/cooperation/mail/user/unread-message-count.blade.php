@component('mail::message')

<?php
$cooperationHoomdossierLink = route('cooperation.auth.login', ['cooperation' => $userCooperation]);

$href = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:".$userCooperation->cooperation_email;
$cooperationWebsiteHref = '<a target="_blank" href="'.$href.'">'.$userCooperation->name.'</a>'
?>

@lang('cooperation/mail/unread-message-count.salutation', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name
])
@lang('cooperation/mail/unread-message-count.text', [
    'unread_message_count' => $unreadMessageCount,
])

@component('mail::button', ['url' => $cooperationHoomdossierLink])
    @lang('cooperation/mail/unread-message-count.button')
@endcomponent

@lang('cooperation/mail/unread-message-count.button-does-not-work')
<br>
<a target="_blank" href="{{$cooperationHoomdossierLink}}">http://{{$userCooperation->slug}}.{{config('app.domain')}} </a>
<br>
<br>
@lang('cooperation/mail/unread-message-count.if-you-have-any-questions', [
    'cooperation_link' => $cooperationWebsiteHref
])
<br>
<br>
@lang('cooperation/mail/unread-message-count.kind_regards', ['app_name' => config('app.name')])
@endcomponent