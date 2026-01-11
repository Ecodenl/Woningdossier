<div wire:poll.10s>
    <a href="{{$messageUrl}}" class="flex flex-wrap justify-center items-center relative">
        <i class="icon-md icon-chat"></i>
        @if($messageCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-5 h-5 px-1 text-xs font-bold text-white bg-red rounded-full">
                {{ $messageCount > 99 ? '99+' : $messageCount }}
            </span>
        @endif
    </a>
</div>
