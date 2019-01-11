@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.my-account.messages.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.my-account.layouts.components.chat-messages')
                        @forelse($mainMessages as $mainMessage)
                            <a href="{{ route('cooperation.my-account.messages.edit', ['cooperation' => $cooperation, 'mainMessageId' => $mainMessage->id]) }}">
                                <li class="left clearfix">

                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong class="primary-font">
                                                {{ $mainMessage->getSender() }}
                                            </strong>

                                            <small class="pull-right text-muted">
                                                @if($mainMessage->hasUserUnreadMessages())
                                                    <span class="label label-primary">@lang('default.new-message')</span>
                                                @endif
                                                <?php $time = \Carbon\Carbon::parse($mainMessage->created_at); ?>
                                                <span class="glyphicon glyphicon-time"></span> {{ $time->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p>
                                            @if($mainMessage->hasUserUnreadMessages())
                                                <strong>
                                                    {{ $mainMessage->message }}
                                                </strong>
                                            @else
                                                {{ $mainMessage->message }}
                                            @endif
                                        </p>
                                    </div>
                                </li>
                            </a>

                        @empty
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
                        @endforelse
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection


