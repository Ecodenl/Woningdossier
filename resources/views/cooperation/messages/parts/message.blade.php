<li class="@if($privateMessage->isMyMessage()) right @else left @endif">
    <div class="chat-body py-1">
        @if($privateMessage->isMyMessage())
            <strong class="float-right text-sm text-blue">{{$privateMessage->getSender()}}</strong>
            <small class="text-sm text-blue flex items-center">
                <span class="icon-sm icon-timer mr-1"></span>{{$privateMessage->created_at->diffForHumans()}}
            </small>
        @else
            <strong class="text-sm text-blue">{{$privateMessage->getSender()}}</strong>
            <small class="float-right text-sm text-blue flex items-center">
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