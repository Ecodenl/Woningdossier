<form action="{{route('cooperation.my-account.messages.store', ['cooperation' => $cooperation])}}" method="post">
    {{csrf_field()}}
    <div class="input-group">
        <input type="hidden" name="receiver_id" value="{{$privateMessages->first()->from_user_id}}">
        <input type="hidden" name="main_message_id" value="{{$privateMessages->sortBy('created_at')->first()->id}}">
        <input id="btn-input" autofocus autocomplete="false" name="message" type="text" class="form-control input-md" placeholder="@lang('woningdossier.cooperation.my-account.messages.edit.chat.input')" />
        <span class="input-group-btn">
            {{$slot}}
        </span>
    </div>
</form>