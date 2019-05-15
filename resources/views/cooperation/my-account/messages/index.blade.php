@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('my-account.messages.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.my-account.layouts.components.chat-messages')
                        @forelse($groups as $group)
                            <a href="{{ route('cooperation.my-account.messages.edit', ['cooperation' => $cooperation, 'mainMessageId' => $group->id]) }}">
                                <li class="left clearfix">

                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <strong class="primary-font">
                                                {{ $group->getSender() }}
                                            </strong>

                                            <small class="pull-right text-muted">
                                                @if($group->hasUserUnreadMessages())
                                                    <span class="label label-primary">@lang('default.new-message')</span>
                                                @endif
                                                <span class="glyphicon glyphicon-time"></span> {{ $group->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p>
                                            @if($group->hasUserUnreadMessages())
                                                <strong>
                                                    {{ $group->message }}
                                                </strong>
                                            @else
                                                {{ $group->message }}
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
                                                @lang('my-account.messages.index.chat.no-messages.title')
                                            </strong>

                                        </div>
                                        <p>
                                            @lang('my-account.messages.index.chat.no-messages.text')
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


