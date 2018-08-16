@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.coordinator.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.admin.layouts.components.chat-messages')
                        @forelse($openConversationRequests as $openConversationRequest)
                            <a href="{{route('cooperation.admin.cooperation.coordinator.conversation-requests.show', ['messageId' => $openConversationRequest->id])}}">
                                <li class="left clearfix">

                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong class="primary-font">
                                                {{$openConversationRequest->getSender($openConversationRequest->id)->first_name. ' ' .$openConversationRequest->getSender($openConversationRequest->id)->last_name}} - {{ $openConversationRequest->title }}
                                            </strong>

                                            <small class="pull-right text-muted">
                                                <?php $time = \Carbon\Carbon::parse($openConversationRequest->created_at) ?>
                                                <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p>
                                            @if($openConversationRequest->hasUserUnreadMessages() || $openConversationRequest->isRead() == false)
                                                <strong>
                                                    {{$openConversationRequest->message}}
                                                </strong>
                                            @else
                                                {{$openConversationRequest->message}}
                                            @endif
                                        </p>
                                    </div>
                                </li>
                            </a>

                        @empty
                            @slot('additionalMessage')
                                <li class="left clearfix">

                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong class="primary-font">
                                                @lang('woningdossier.cooperation.my-account.conversation-requests.index.chat.no-messages.title')
                                            </strong>

                                        </div>
                                        <p>
                                            @lang('woningdossier.cooperation.my-account.conversation-requests.index.chat.no-messages.text')
                                        </p>
                                    </div>
                                </li>
                            @endslot
                        @endforelse
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection



