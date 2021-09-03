@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
        <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 rounded-lg py-2 border-b border-solid border-blue-500 border-opacity-50">
            @include('cooperation.messages.parts.group-participants', ['groupParticipants' => $groupParticipants, 'buildingId' => $buildingId])
        </div>
        <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-2 panel-chat-body" id="chat">
            @component('cooperation.messages.parts.messages', ['tailwind' => true])
                @foreach($privateMessages as $privateMessage)
                    @include('cooperation.messages.parts.message', ['privateMessage' => $privateMessage, 'tailwind' => true])
                @endforeach
            @endcomponent
        </div>
        <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-2 rounded-b-lg border-t border-solid border-blue-500 border-opacity-50">
            @component('cooperation.messages.parts.input', [
                'privateMessages' => $privateMessages,
                'buildingId' => \App\Helpers\HoomdossierSession::getBuilding(),
                'url' => route('cooperation.my-account.messages.store'),
                'tailwind' => true,
             ])
                <button type="submit" class="btn btn-green" id="btn-chat">
                    @lang('my-account.messages.edit.chat.button')
                </button>
            @endcomponent
        </div>
    </div>
@endsection

@push('js')
    <script>
        $('document').ready(function () {
            var chat = $('.panel-chat-body')[0];
            if (typeof chat !== "undefined") {
                chat.scrollTop = chat.scrollHeight - chat.clientHeight;
            }
        })
    </script>
@endpush