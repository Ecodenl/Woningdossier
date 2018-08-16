@extends('cooperation.admin.coach.layouts.app')


@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{$privateMessages->first()->title}}
        </div>
        <div class="panel-body panel-chat-body" id="chat">
            @component('cooperation.layouts.chat.messages')
                @forelse($privateMessages->sortBy('created_at') as $privateMessage)

                    <?php $time = \Carbon\Carbon::parse($privateMessage->created_at) ?>

                    <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                        <div class="chat-body clearfix">
                            <div class="header">
                                @if($privateMessage->isMyMessage())

                                    <small class="text-muted"><span class="glyphicon glyphicon-time"></span>{{$time->diffForHumans()}}</small>
                                    <strong class="pull-right primary-font">{{$privateMessage->getSender($privateMessage->id)->first_name}}</strong>

                                @else

                                    <strong class="primary-font">{{$privateMessage->getSender($privateMessage->id)->first_name}}</strong>
                                    <small class="pull-right text-muted"><span class="glyphicon glyphicon-time"></span>{{$time->diffForHumans()}}</small>

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
            @component('cooperation.layouts.chat.input', ['privateMessages' => $privateMessages, 'url' => route('cooperation.admin.coach.messages.store')])
                <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                    @lang('woningdossier.cooperation.admin.coach.messages.edit.send')
                </button>
            @endcomponent
        </div>
    </div>


@endsection