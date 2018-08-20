@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.coach.messages.index.header')
            <a href="{{route('cooperation.admin.coach.connect-to-resident.index')}}" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-envelope"></span></a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.admin.layouts.components.chat-messages')
                        @forelse($mainMessages->sortByDesc('created_at') as $mainMessage)
                            <a href="{{route('cooperation.admin.coach.messages.edit', ['messageId' => $mainMessage->id])}}">
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
                                            @if($mainMessage->isRead() == false)
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



