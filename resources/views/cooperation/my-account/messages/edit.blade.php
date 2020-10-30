@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    @if(!\App\Helpers\Hoomdossier::user()->hasMultipleRoles())
        <div class="panel panel-default">
            <div class="panel-heading">
                @include('cooperation.messages.parts.group-participants', ['groupParticipants' => $groupParticipants, 'buildingId' => $buildingId])
            </div>
            <div class="panel-body panel-chat-body" id="chat">
                @component('cooperation.messages.parts.messages')
                    @foreach($privateMessages as $privateMessage)
                        @include('cooperation.messages.parts.message', compact('privateMessage'))
                    @endforeach
                @endcomponent
            </div>
            <div class="panel-footer">
                @component('cooperation.messages.parts.input', [
                    'privateMessages' => $privateMessages,
                    'buildingId' => \App\Helpers\HoomdossierSession::getBuilding(),
                    'url' => route('cooperation.my-account.messages.store')
                 ])
                    <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                        @lang('my-account.messages.edit.chat.button')
                    </button>
                @endcomponent
            </div>
        </div>
    @else
        @component('cooperation.tool.components.alert')
            @lang('cooperation/my-account/messages.edit.alert')
        @endcomponent
    @endcan


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