@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.conversation-requests.show.header', ['firstName' => $privateMessage->getSender($privateMessage->id)->first_name, 'lastName' => $privateMessage->getSender($privateMessage->id)->last_name])

            <a href="{{route('cooperation.admin.cooperation.coordinator.connect-to-coach.create', ['senderId' => $privateMessage->from_user_id])}}" class="pull-right btn btn-primary btn-xs">
                <span class="glyphicon glyphicon-user"></span>
                Koppelen aan coach
            </a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.admin.layouts.components.chat-messages')
                        <?php $time = \Carbon\Carbon::parse($privateMessage->created_at) ?>

                        <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                            <div class="chat-body clearfix">
                                <div class="header">
                                    @if($privateMessage->isMyMessage())

                                        <small class="text-muted">
                                            <span class="glyphicon glyphicon-time"></span>{{$time->diffForHumans()}}
                                        </small>
                                        <strong class="pull-right primary-font">{{$privateMessage->getSender($privateMessage->id)->first_name}}</strong>

                                    @else

                                        <strong class="primary-font">{{$privateMessage->getSender($privateMessage->id)->first_name}}</strong>
                                        <small class="pull-right text-muted">
                                            <span class="glyphicon glyphicon-time"></span>{{$time->diffForHumans()}}
                                        </small>

                                    @endif
                                </div>
                                <p>
                                    {{$privateMessage->message}}
                                </p>
                            </div>
                        </li>
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection