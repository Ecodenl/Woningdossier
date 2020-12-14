<?php
$cooperationHoomdossierLink = route('cooperation.auth.login', ['cooperation' => $userCooperation]);
$cooperationContact = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : "mailto:".$userCooperation->cooperation_email;
?>

@lang('cooperation/mail/unread-message-count.salutation', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name
])
<br><br>
@lang('cooperation/mail/unread-message-count.text', [
    'unread_message_count' => $unreadMessageCount,
])
<br><br>

<a href="{{$cooperationHoomdossierLink}}">@lang('cooperation/mail/unread-message-count.button')</a>
<br>
<br>

@lang('cooperation/mail/unread-message-count.if-you-have-any-questions', [
    'cooperation_link' =>  "<a href='${cooperationContact}'>${cooperationContact}</a>"
])
<br>
<br>
@lang('cooperation/mail/unread-message-count.kind_regards', ['app_name' => config('app.name')])