@if($showParticipants)
    <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 rounded-lg py-2 border-b border-solid border-blue-500 border-opacity-50">
        @include('cooperation.messages.parts.group-participants', ['groupParticipants' => $groupParticipants, 'buildingId' => $building->id])
    </div>
@endif
<div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-2 panel-chat-body">
    @component('cooperation.messages.parts.messages',)
        @foreach($privateMessages as $privateMessage)
            @include('cooperation.messages.parts.message', ['privateMessage' => $privateMessage])
        @endforeach
    @endcomponent
</div>
<div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-2 rounded-b-lg border-t border-solid border-blue-500 border-opacity-50">
    @component('cooperation.messages.parts.input', compact('building', 'url', 'isPublic'))
        <button type="submit" class="btn btn-green">
            @lang('my-account.messages.edit.chat.button')
        </button>
    @endcomponent
</div>

@push('js')
    <script type="module">
        window.setChatScroll = function () {
            document.querySelectorAll('.panel-chat-body').forEach(chat => {
                chat.scrollTop = chat.scrollHeight - chat.clientHeight;
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(() => {
                setChatScroll();
            });
        })
    </script>
@endpush