@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('cooperation.my-account.messages.side-nav')
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @lang('woningdossier.cooperation.my-account.messages.index.header')
                    </div>

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">
                                <ul class="chat">
                                    @if(isset($coachConversationRequest) && $coachConversationRequest->status == "in behandeling")
                                        <li class="left clearfix">

                                            <div class="chat-body clearfix">
                                                <div class="header">
                                                    <strong class="primary-font">
                                                        @lang('woningdossier.cooperation.my-account.messages.index.chat.coach-conversation-consideration.title')
                                                    </strong>

                                                </div>
                                                <p>
                                                    @lang('woningdossier.cooperation.my-account.messages.index.chat.coach-conversation-consideration.text')
                                                </p>
                                            </div>
                                        </li>
                                    @endif
                                    @forelse($mainMessages as $mainMessage)

                                        <a href="{{route('cooperation.my-account.messages.edit', ['cooperation' => $cooperation, 'mainMessageId' => $mainMessage->id])}}">
                                            <li class="left clearfix">

                                                <div class="chat-body clearfix">
                                                    <div class="header">
                                                        <strong class="primary-font">
                                                            {{$mainMessage->getSender($mainMessage->id)->first_name. ' ' .$mainMessage->getSender($mainMessage->id)->last_name}} - {{ $mainMessage->title }}
                                                        </strong>

                                                        <small class="pull-right text-muted">
                                                            <?php $time = \Carbon\Carbon::parse($mainMessage->created_at) ?>
                                                            <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    <p>
                                                        @if($mainMessage->hasUserUnreadMessages() || $mainMessage->isRead() == false)
                                                            <strong>
                                                                {{$mainMessage->message}}
                                                            </strong>
                                                        @else
                                                            {{$mainMessage->message}}
                                                        @endif
                                                    </p>
                                                </div>
                                            </li>
                                        </a>

                                    @empty
                                        @if(isset($coachConversationRequest) != true)
                                        <li class="left clearfix">

                                            <div class="chat-body clearfix">
                                                <div class="header">
                                                    <strong class="primary-font">
                                                        @lang('woningdossier.cooperation.my-account.messages.index.chat.no-messages.title')
                                                    </strong>

                                                </div>
                                                <p>
                                                    @lang('woningdossier.cooperation.my-account.messages.index.chat.no-messages.text')
                                                </p>
                                            </div>
                                        </li>
                                        @endif
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
