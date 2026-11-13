<div class="flex flex-row items-center space-x-8 xl:space-x-12" wire:poll.10s>
    @if($canViewFiles)
        <a href="{{ $fileUrl }}"
           class="nav-item @if(RouteLogic::inSharedFiles(Route::currentRouteName())) is-active @endif">
            {{-- The count is part of the label rather than a badge: the badge on the messages
                 means unread, and the files have no such thing as unread. --}}
            {{ trans_choice('cooperation/frontend/layouts.nav.files', $fileCount, ['count' => $fileCount]) }}
        </a>
    @endif

    @if($canViewMessages)
        <a href="{{ $messageUrl }}"
           class="nav-item @if(RouteLogic::inMessages(Route::currentRouteName())) is-active @endif">
            @lang('cooperation/frontend/layouts.nav.messages')
            @if($messageCount > 0)
                <span class="nav-badge">{{ $messageCount > 99 ? '99+' : $messageCount }}</span>
            @endif
        </a>
    @endif
</div>
