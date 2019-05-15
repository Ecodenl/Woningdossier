<form action="{{route('cooperation.my-account.messages.store', ['cooperation' => $cooperation])}}" method="post">
    {{csrf_field()}}
    <div class="input-group">
        <input type="hidden" name="receiver_id" value="{{$privateMessages->first()->from_user_id}}">
        <input type="hidden" name="main_message_id" value="{{$privateMessages->sortBy('created_at')->first()->id}}">
        <input id="btn-input" autofocus autocomplete="false" name="message" type="text" class="form-control input-md" placeholder="@lang('my-account.messages.edit.chat.input')" />
        <span class="input-group-btn">
            {{$slot}}
        </span>
    </div>
</form>

@push('js')
    <script>
        $(document).ready(function () {
            // onload scroll the chat to the bottom
            // same as code beneath but with a "animation"
            // $('#chat').animate({ scrollTop: ($('#chat')[0].scrollHeight)}, 1000);
            $('#chat').scrollTop($('#chat')[0].scrollHeight);
        });

        var chat = $('#chat')[0];

        var chatMessages = $(chat).find('li').length;

        var add = setInterval(function() {

            var isScrolledToBottom = chat.scrollHeight - chat.clientHeight <= chat.scrollTop + 1;

            if(isScrolledToBottom) {
                chat.scrollTop = chat.scrollHeight - chat.clientHeight;
            }

        }, 1000);


    </script>
@endpush
