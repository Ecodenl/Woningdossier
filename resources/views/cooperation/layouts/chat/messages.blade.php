<div class="row">
    <div class="col-md-12">
        <div class="panel-collapse " id="collapseOne">
            <ul class="chat">
                {{$slot}}
            </ul>
        </div>
    </div>
</div>

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
