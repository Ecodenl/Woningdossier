@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.coordinator.messages.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.admin.layouts.components.chat-messages')
                        @forelse($mainMessages as $mainMessage)
                            <a href="{{route('cooperation.admin.cooperation.coordinator.messages.edit', ['messageId' => $mainMessage->id])}}">
                                <li class="left clearfix">

                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong class="primary-font">
                                                @if($mainMessage->getSender($mainMessage->id) instanceof \App\Models\User)
                                                {{ $mainMessage->getSender($mainMessage->id)->first_name. ' ' .$mainMessage->getSender($mainMessage->id)->last_name }} - {{ $mainMessage->title }}
                                                @elseif($mainMessage->getReceivingCooperation() instanceof \App\Models\Cooperation)
                                                {{ $mainMessage->getReceivingCooperation()->name }} - {{ $mainMessage->title }}
                                                @else
                                                {{ $mainMessage->title }}
                                                @endif
                                            </strong>

                                            <small class="pull-right text-muted">
                                                @if($mainMessage->hasUserUnreadMessages())
                                                    <span class="label label-primary">@lang('default.new-message')</span>
                                                @endif
                                                <?php $time = \Carbon\Carbon::parse($mainMessage->created_at) ?>
                                                <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p>
                                            @if($mainMessage->hasUserUnreadMessages())
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
                            @slot('additionalMessage')
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
                            @endslot
                        @endforelse
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection



