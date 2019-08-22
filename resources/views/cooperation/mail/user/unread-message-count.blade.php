@lang('mail.unread-message-count.salutation', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name
])
<br><br>

<?php
    $cooperationHoomdossierLink = route('cooperation.login', $userCooperation);
    $cooperationHoomdossierHref = '<a target="_blank" href="'.$cooperationHoomdossierLink.'">http://'.$userCooperation->slug.'.'.config('app.domain').'</a>';

    $cooperationWebsiteHref = '<a target="_blank" href="'.$userCooperation->website_url.'">'.$userCooperation->name.'</a>';
?>

@lang('mail.unread-message-count.text', [
    'unread_message_count' => $unreadMessageCount,
    'hoomdossier_link' => $cooperationHoomdossierHref
])

<br><br>
@lang('mail.if-you-have-any-questions', [
    'cooperation_link' => $cooperationWebsiteHref
])
<br><br>
@lang('mail.unread-message-count.kind_regards', ['app_name' => config('app.name')])