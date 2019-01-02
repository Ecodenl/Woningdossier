<?php
    $receiver = $privateMessages->where('from_user_id', '==', Auth::id())->first();
    if ($receiver instanceof \App\Models\PrivateMessage) {
        $receiverId = $receiver->to_user_id;
        if ($receiverId == Auth::id()) {
            $receiverId = $receiver->from_user_id;
        }
    } else {
        $receiver = $privateMessages->where('to_user_id', '==', Auth::id())->first();

        $receiverId = $receiver->to_user_id;
        if ($receiverId == Auth::id()) {
            $receiverId = $receiver->from_user_id;
        }
    }
?>

<form action="{{$url}}" method="post" style="margin-bottom: unset;">
    {{csrf_field()}}
    <div class="input-group">
        <input type="hidden" name="receiver_id" value="{{$receiverId}}">
        <input type="hidden" name="main_message_id" value="{{$privateMessages->sortBy('created_at')->first()->id}}">
        <input id="btn-input" autofocus autocomplete="false" name="message" type="text" class="form-control input-md" placeholder="@lang('woningdossier.cooperation.my-account.messages.edit.chat.input')" />
        <span class="input-group-btn">
            {{$slot}}
        </span>
    </div>
</form>

