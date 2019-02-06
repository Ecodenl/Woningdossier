@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @include('cooperation.layouts.chat.group-participants', ['groupParticipants' => $groupParticipants, 'buildingId' => $buildingId, 'isPublic' => $isPublic])
        </div>
        <div class="panel-body panel-chat-body" id="chat">
            <form id="revoke-access-form" action="{{ route('cooperation.admin.coach.messages.revoke-access') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="building_id" value="{{$buildingId}}">
            </form>
            @component('cooperation.layouts.chat.messages')
                @forelse($privateMessages as $privateMessage)


                    <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                        <div class="chat-body clearfix">
                            <div class="header">
                                @if($privateMessage->isMyMessage())

                                    <small class="text-muted"><span class="glyphicon glyphicon-time"></span>{{ $privateMessage->created_at->diffForHumans() }}</small>
                                    <strong class="pull-right primary-font">{{ $privateMessage->getSender()}}</strong>

                                @else

                                    <strong class="primary-font">{{ $privateMessage->getSender() }}</strong>
                                    <small class="pull-right text-muted"><span class="glyphicon glyphicon-time"></span>{{ $privateMessage->created_at->diffForHumans() }}</small>

                                @endif
                            </div>
                            <p>
                                {{ $privateMessage->message }}
                            </p>
                        </div>
                    </li>
                @empty

                @endforelse
            @endcomponent
        </div>

        <div class="panel-footer">
            @component('cooperation.layouts.chat.input', ['privateMessages' => $privateMessages, 'buildingId' => $buildingId, 'url' => route('cooperation.admin.coach.messages.store'), 'isPublic' => $isPublic])
                <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                    @lang('woningdossier.cooperation.admin.coach.messages.edit.send')
                </button>
            @endcomponent
        </div>
    </div>


@endsection

@push('js')
    <script>
        $('document').ready(function () {
            $('#revoke-access').on('click', function () {
                if (confirm('Weet u zeker dat u geen contact wilt met deze coach? Er wordt hierna een nieuwe coach voor u gezocht. Dit kan enige tijd duren.')) {
                    $('#revoke-access-form').submit();
                }
            });
        })
    </script>
@endpush