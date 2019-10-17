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
            <?php
                $messagesThatMayContainHtml = [
                    \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION,
                    \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION,
                    \App\Models\PrivateMessage::REQUEST_TYPE_OTHER,
                    \App\Models\PrivateMessage::REQUEST_TYPE_MEASURE,
                ];
            ?>
            @if(in_array($privateMessage->request_type, $messagesThatMayContainHtml))
                {!! $privateMessage->message !!}
            @else
                {{ $privateMessage->message }}
            @endif
        </p>
    </div>
</li>