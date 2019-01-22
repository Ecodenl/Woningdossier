@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.coach.messages.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.admin.layouts.components.chat-messages')
                        @forelse($activeBuildingCoachStatuses as $activeBuildingCoachStatus)
                            <?php
                                $publicPrivateMessage = \App\Models\PrivateMessage::forMyCooperation()->public()->conversation($activeBuildingCoachStatus->building_id)->get()->last();
                                $privatePrivateMessage = \App\Models\PrivateMessage::forMyCooperation()->private()->conversation($activeBuildingCoachStatus->building_id)->get()->last();
                            ?>
                            @if($privatePrivateMessage instanceof \App\Models\PrivateMessage)
                                <a href="{{ route('cooperation.admin.coach.messages.private.edit', ['buildingId' => $activeBuildingCoachStatus->building_id]) }}">
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
                                                    <span class="glyphicon glyphicon-time"></span> {{ $privatePrivateMessage->created_at->diffForHumans() }}
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
                                <a href="{{ route('cooperation.admin.coach.messages.public.edit', ['buildingId' => $activeBuildingCoachStatus->building_id]) }}">
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
                                                <span class="glyphicon glyphicon-time"></span> {{ $publicPrivateMessage->created_at->diffForHumans() }}
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
        {{--<div class="panel-footer">--}}
            {{--<div class="btn-group btn-group-justified">--}}
                {{--<a href="#" class="btn btn-success">@lang('woningdossier.cooperation.admin.coach.messages.index.filter.residents')</a>--}}
                {{--<a href="#" class="btn btn-primary">@lang('woningdossier.cooperation.admin.coach.messages.index.filter.coordinators')</a>--}}
            {{--</div>--}}

        {{--</div>--}}

    </div>
@endsection



