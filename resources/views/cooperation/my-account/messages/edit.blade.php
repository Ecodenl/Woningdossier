@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            @include('cooperation.my-account.messages.side-nav')

            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.messages.edit.header') - {{$privateMessages->first()->title}}</div>

                    <div id="chat" class="panel-body panel-chat-body">


                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel-collapse " id="collapseOne">
                                    <ul class="chat">
                                        @forelse($privateMessages->sortBy('created_at') as $privateMessage)

                                            <?php $time = \Carbon\Carbon::parse($privateMessage->created_at) ?>
                                            <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                                                <div class="chat-body clearfix">
                                                    <div class="header">
                                                        @if($privateMessage->isMyMessage())

                                                            <small class="text-muted"><span class="glyphicon glyphicon-time"></span>{{$time->diffForHumans()}}</small>
                                                            <strong class="pull-right primary-font">{{$privateMessage->getSender($privateMessage->id)->first_name}}</strong>
                                                        @else
                                                            <strong class="primary-font">{{$privateMessage->getSender($privateMessage->id)->first_name}}</strong>
                                                            <small class="pull-right text-muted"><span class="glyphicon glyphicon-time"></span>{{$time->diffForHumans()}}</small>
                                                        @endif
                                                    </div>
                                                    <p>
                                                        {{$privateMessage->message}}
                                                    </p>
                                                </div>
                                            </li>
                                        @empty

                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="panel-footer">
                        <form action="{{route('cooperation.my-account.messages.store', ['cooperation' => $cooperation])}}" method="post">
                            {{csrf_field()}}
                            <div class="input-group">
                                <input type="hidden" name="receiver_id" value="{{$privateMessages->first()->from_user_id}}">
                                <input type="hidden" name="main_message_id" value="{{$privateMessages->sortBy('created_at')->first()->id}}">
                                <input id="btn-input" autofocus autocomplete="false" name="message" type="text" class="form-control input-md" placeholder="@lang('woningdossier.cooperation.my-account.messages.edit.chat.input')" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                                        @lang('woningdossier.cooperation.my-account.messages.edit.chat.button')
                                    </button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function () {
            // onload scroll the chat to the bottom
            // same as code beneath but with a "animation"
            // $('#chat').animate({ scrollTop: ($('#chat')[0].scrollHeight)}, 1000);
            $('#chat').scrollTop($('#chat')[0].scrollHeight);
        });

        var chat = $('#chat')[0];

        var chatMessages = $(chat).find('li').length;

        var add = setInterval(function() {

            var isScrolledToBottom = chat.scrollHeight - chat.clientHeight <= chat.scrollTop + 1;

            if(isScrolledToBottom) {
                chat.scrollTop = chat.scrollHeight - chat.clientHeight;
            }

        }, 1000);


    </script>
@endpush
