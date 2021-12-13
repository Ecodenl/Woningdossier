{{-- Legacy support --}}
@if(($tailwind ?? false))
    <li class="@if($privateMessage->isMyMessage()) right @else left @endif">
        <div class="chat-body py-1">
            @if($privateMessage->isMyMessage())
                <strong class="float-right text-sm text-blue-500">{{$privateMessage->getSender()}}</strong>
                <small class="text-sm text-blue-500 flex items-center">
                    <span class="icon-sm icon-timer mr-1"></span>{{$privateMessage->created_at->diffForHumans()}}
                </small>
            @else
                <strong class="text-sm text-blue-500">{{$privateMessage->getSender()}}</strong>
                <small class="float-right text-sm text-blue-500 flex items-center">
                    <span class="icon-sm icon-timer mr-1"></span>{{$privateMessage->created_at->diffForHumans()}}
                </small>
            @endif
            <p>
                {{--
                    This may seems to be dangerous (which it somewhat is). But strip_tags is used on the message, without the second param
                    Sometimes a prefix is added which contains html, so we still need to {!!  !!}
                --}}
                {!! $privateMessage->message !!}
            </p>
        </div>
    </li>
@else
    <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
        <div class="chat-body clearfix">
            <div class="header">
                @if($privateMessage->isMyMessage())
                    <small class="text-muted">
                        <span class="glyphicon glyphicon-time"></span>{{$privateMessage->created_at->diffForHumans()}}
                    </small>
                    <strong class="pull-right primary-font">{{$privateMessage->getSender()}}</strong>
                @else
                    <strong class="primary-font">{{$privateMessage->getSender()}}</strong>
                    <small class="pull-right text-muted">
                        <span class="glyphicon glyphicon-time"></span>{{$privateMessage->created_at->diffForHumans()}}
                    </small>
                @endif
            </div>
            <p>
                {{--
                    This may seems to be dangerous (which it somewhat is). But strip_tags is used on the message, without the second param
                    Sometimes a prefix is added which contains html, so we still need to {!!  !!}
                --}}
                {!! $privateMessage->message !!}
            </p>
        </div>
    </li>
@endif