@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @include('cooperation.layouts.chat.group-participants', ['groupParticipants' => $groupParticipants, 'buildingId' => $buildingId, 'isPublic' => $isPublic])
        </div>
        <div class="panel-body panel-chat-body" id="chat">
            @component('cooperation.layouts.chat.messages')
                @forelse($privateMessages as $privateMessage)


                    <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                        <div class="chat-body clearfix">
                            <div class="header">
                                @if($privateMessage->isMyMessage())

                                    <small class="text-muted"><span class="glyphicon glyphicon-time"></span>{{$privateMessage->created_at->diffForHumans()}}</small>
                                    <strong class="pull-right primary-font">{{$privateMessage->getSender()}}</strong>

                                @else

                                    <strong class="primary-font">{{$privateMessage->getSender()}}</strong>
                                    <small class="pull-right text-muted"><span class="glyphicon glyphicon-time"></span>{{$privateMessage->created_at->diffForHumans()}}</small>

                                @endif
                            </div>
                            <p>
                                {{$privateMessage->message}}
                            </p>
                        </div>
                    </li>
                @empty

                @endforelse
            @endcomponent
        </div>

        <div class="panel-footer">
            @component('cooperation.layouts.chat.input', ['privateMessages' => $privateMessages, 'buildingId' => $buildingId, 'url' => route('cooperation.admin.cooperation.cooperation-admin.messages.store'), 'isPublic' => $isPublic])
                <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                    @lang('woningdossier.cooperation.admin.coach.messages.edit.send')
                </button>
            @endcomponent
        </div>
    </div>


@endsection