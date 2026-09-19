<div class="flex flex-row items-center space-x-8 xl:space-x-12" wire:poll.10s>
    @if($canViewFiles)
        <a href="{{ $fileUrl }}"
           class="nav-item no-underline @if(RouteLogic::inSharedFiles(Route::currentRouteName())) is-active @endif">
            @lang('cooperation/frontend/layouts.nav.shared-files')
            @if($fileCount > 0)
                <span class="nav-badge">{{ $fileCount > 99 ? '99+' : $fileCount }}</span>
            @endif
        </a>
    @endif

    @if($canViewMessages)
        <a href="{{ $messageUrl }}"
           class="nav-item no-underline @if(RouteLogic::inMessages(Route::currentRouteName())) is-active @endif">
            @lang('cooperation/frontend/layouts.nav.messages')
            @if($messageCount > 0)
                <span class="nav-badge">{{ $messageCount > 99 ? '99+' : $messageCount }}</span>
            @endif
        </a>
    @endif
</div>
