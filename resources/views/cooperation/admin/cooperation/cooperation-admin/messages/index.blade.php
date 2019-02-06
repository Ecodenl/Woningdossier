@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.messages.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.admin.layouts.components.chat-messages')
                        @forelse($buildings as $building)
                            <?php
                                $publicPrivateMessage = \App\Models\PrivateMessage::forMyCooperation()->public()->conversation($building->id)->get()->last();
                                $privatePrivateMessage = \App\Models\PrivateMessage::forMyCooperation()->private()->conversation($building->id)->get()->last();
                            ?>
                            @if($privatePrivateMessage instanceof \App\Models\PrivateMessage)
                                <a href="{{ route('cooperation.admin.cooperation.cooperation-admin.messages.private.edit', ['buildingId' => $building->id]) }}">
                                    <li class="left clearfix">
                                        <div class="chat-body clearfix">
                                            <div class="header">
                                                <strong class="primary-font">
                                                    <p>{{$privatePrivateMessage->building()->withTrashed()->first()->getFullAddress()}}</p>
                                                    {{ $privatePrivateMessage->getSender() }}
                                                </strong>

                                                <small class="pull-right text-muted">
                                                    <span class="label label-warning">Deze chat is prive.</span>
                                                    @if(\App\Models\PrivateMessageView::isMessageUnread($privatePrivateMessage))
                                                        <span class="label label-primary">@lang('default.new-message')</span>
                                                    @endif
                                                    <?php $time = \Carbon\Carbon::parse($privatePrivateMessage->created_at); ?>
                                                    <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p>
                                                @if(\App\Models\PrivateMessageView::isMessageUnread($privatePrivateMessage))
                                                    <strong>
                                                        {{ $privatePrivateMessage->message }}
                                                    </strong>
                                                @else
                                                    {{ $privatePrivateMessage->message }}
                                                @endif
                                            </p>
                                        </div>
                                    </li>
                                </a>
                            @endif
                            @if($publicPrivateMessage instanceof \App\Models\PrivateMessage)
                                <a href="{{ route('cooperation.admin.cooperation.cooperation-admin.messages.public.edit', ['buildingId' => $building->id]) }}">
                                    <li class="left clearfix">
                                        <div class="chat-body clearfix">
                                            <div class="header">
                                                <strong class="primary-font">
                                                    <p>{{$publicPrivateMessage->building()->withTrashed()->first()->getFullAddress()}}</p>
                                                    {{ $publicPrivateMessage->getSender() }}
                                                </strong>

                                                <small class="pull-right text-muted">
                                                    <span class="label label-danger">Deze chat is publiek.</span>
                                                    @if(\App\Models\PrivateMessageView::isMessageUnread($publicPrivateMessage))
                                                        <span class="label label-primary">@lang('default.new-message')</span>
                                                    @endif
                                                    <?php $time = \Carbon\Carbon::parse($publicPrivateMessage->created_at); ?>
                                                    <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p>
                                                @if(\App\Models\PrivateMessageView::isMessageUnread($publicPrivateMessage))
                                                    <strong>
                                                        {{ $publicPrivateMessage->message }}
                                                    </strong>
                                                @else
                                                    {{ $publicPrivateMessage->message }}
                                                @endif
                                            </p>
                                        </div>
                                    </li>
                                </a>
                            @endif

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



