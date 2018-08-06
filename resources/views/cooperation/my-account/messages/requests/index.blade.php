@extends('cooperation.my-account.messages.layout')

@section('messages_header', __('woningdossier.cooperation.my-account.messages.requests.index.header'))

@section('messages_content')
    <div class="row">
        <div class="col-md-12">
            <ul class="chat">
                @forelse($conversationRequests as $conversationRequest)

                    @if($conversationRequest->isCoachRequestConversation() && \App\Models\PrivateMessage::hasUserResponseToCoachConversationRequest())

                    @else

                        <a @if($conversationRequest->isCoachRequestConversation()) href="{{route('cooperation.conversation-requests.coach.index', ['cooperation' => $cooperation])}}" @else href="{{route('cooperation.my-account.messages.requests.edit', ['cooperation' => $cooperation, 'requestMessageId' => $conversationRequest->id])}}" @endif>
                            <li class="left clearfix">

                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">
                                            {{$conversationRequest->getSender($conversationRequest->id)->first_name. ' ' .$conversationRequest->getSender($conversationRequest->id)->last_name}} - {{ $conversationRequest->title }}
                                        </strong>

                                        <small class="pull-right text-muted">
                                            <?php $time = \Carbon\Carbon::parse($conversationRequest->created_at) ?>
                                            <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p>
                                        @if($conversationRequest->hasUserUnreadMessages() || $conversationRequest->isRead() == false)
                                            <strong>
                                                {{$conversationRequest->message}}
                                            </strong>
                                        @else
                                            {{$conversationRequest->message}}
                                        @endif
                                    </p>
                                </div>
                            </li>
                        </a>

                    @endif
                @empty
                    <li class="left clearfix">

                        <div class="chat-body clearfix">
                            <div class="header">
                                <strong class="primary-font">
                                    @lang('woningdossier.cooperation.my-account.messages.requests.index.chat.no-messages.title')
                                </strong>

                            </div>
                            <p>
                                @lang('woningdossier.cooperation.my-account.messages.requests.index.chat.no-messages.text')
                            </p>
                        </div>
                    </li>
                @endforelse

            </ul>
        </div>
    </div>
@endsection
