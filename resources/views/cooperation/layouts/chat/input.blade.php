<?php

    // Get the last message and check if the user was sender or receiver
    /**
     * @var \Illuminate\Support\Collection
     * @var \App\Models\PrivateMessage     $lastMessage
     */
    $lastMessage = $privateMessages->sortByDesc('created_at')->first();
    $receiverId = null;

    // Coach conversation requests cannot be answered
    if (! in_array($lastMessage->request_type, [\App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION])) {
        if (is_null($receiverId) && $lastMessage->to_user_id == Auth::id()) {
            $receiverId = $lastMessage->from_user_id;
        }
        if (is_null($receiverId) && $lastMessage->from_user_id == Auth::id()) {
            $receiverId = $lastMessage->to_user_id;
        }
        if (is_null($receiverId) && in_array(\App\Helpers\HoomdossierSession::currentRole(), ['coordinator', 'cooperation-admin'])) {
            if ($lastMessage->from_cooperation_id == \App\Helpers\HoomdossierSession::getCooperation()) {
                $receiverId = $lastMessage->to_user_id;
            } elseif ($lastMessage->to_cooperation_id == \App\Helpers\HoomdossierSession::getCooperation()) {
                $receiverId = $lastMessage->from_user_id;
            }
        }
    }
?>

@if(!is_null($receiverId))
<form action="{{ $url }}" method="post" style="margin-bottom: unset;">
    {{ csrf_field() }}
    <div class="input-group">
        <input type="hidden" name="receiver_id" value="{{ $receiverId }}">
        {{--<input type="hidden" name="main_message_id" value="{{ $privateMessages->sortBy('created_at')->first()->id }}">--}}
        <input type="hidden" name="main_message_id" value="{{ $mainMessageId }}">

        <input id="btn-input" required @cannot('respond', $mainMessageId) disabled @endcan autofocus autocomplete="false" name="message" type="text" class="form-control input-md" placeholder="@lang('woningdossier.cooperation.my-account.messages.edit.chat.input')" />

        <span class="input-group-btn">
            {{ $slot }}
        </span>

    </div>
</form>
@endif
