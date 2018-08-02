@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-2">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active">
                        <a href="#">
                            @lang('woningdossier.cooperation.my-account.messages.navigation.inbox')
                            <span class="pull-right badge">{{$messages->count()}}</span>
                        </a>
                    </li>
                    <li ><a href="#">@lang('woningdossier.cooperation.my-account.messages.navigation.edit-coaching-conversation-request')</a></li>
                </ul>
            </div>

            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.messages.index.header')</div>

                    <div class="panel-body">


                        <div class="row">
                            <div class="col-md-12">
                                <ul class="chat">
                                    @forelse($incomingMessages as $incomingMessage)
                                        <a href="{{route('cooperation.my-account.messages.edit', ['cooperation' => $cooperation])}}">
                                            <li class="left clearfix">

                                                <div class="chat-body clearfix">
                                                    <div class="header">
                                                        <strong class="primary-font">
                                                            {{$incomingMessage->getSender($incomingMessage->id)->first_name. ' ' .$incomingMessage->getSender($incomingMessage->id)->last_name}}
                                                        </strong>

                                                        <small class="pull-right text-muted">
                                                            <?php $time = \Carbon\Carbon::parse($incomingMessage->created_at) ?>
                                                            <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    <p>
                                                        {{$incomingMessage->message}}
                                                    </p>
                                                </div>
                                            </li>
                                        </a>
                                    @empty

                                    @endforelse

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
