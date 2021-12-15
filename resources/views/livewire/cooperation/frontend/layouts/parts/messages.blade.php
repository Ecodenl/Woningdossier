<div wire:poll.10s>
    <a href="{{$messageUrl}}" class="flex flex-wrap justify-center items-center">
        @if($messageCount === 0)
            <i class="icon-md icon-chat"></i>
        @else
            <i class="icon-md icon-chat-alert"></i>
        @endif
    </a>
</div>
