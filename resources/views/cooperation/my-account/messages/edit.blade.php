@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-2">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active">
                        <a href="{{route('cooperation.my-account.messages.index', ['cooperation' => $cooperation])}}">
                            @lang('woningdossier.cooperation.my-account.messages.navigation.inbox')
{{--                            <span class="pull-right badge">{{$priv>count()}}</span>--}}
                        </a>
                    </li>
                    <li ><a href="#">@lang('woningdossier.cooperation.my-account.messages.navigation.edit-coaching-conversation-request')</a></li>
                </ul>
            </div>

            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.messages.edit.header')</div>

                    <div class="panel-body panel-chat-body">


                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel-collapse " id="collapseOne">
                                    <ul class="chat">
                                        @forelse($privateMessages as $privateMessage)

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
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="panel-footer">
                        <div class="input-group">
                            <input id="btn-input" type="text" class="form-control input-md" placeholder="@lang('woningdossier.cooperation.my-account.messages.edit.chat.input')" />
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-md" id="btn-chat">
                                    @lang('woningdossier.cooperation.my-account.messages.edit.chat.button')</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
