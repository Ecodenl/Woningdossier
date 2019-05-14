@lang('mail.unread-message-count.salutation', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name
])
<br><br>
@lang('mail.unread-message-count.text', [
    'unread_message_count' => $unreadMessageCount
])
<br><br>
@lang('mail.unread-message-count.kind_regards', ['app_name' => config('app.name')])