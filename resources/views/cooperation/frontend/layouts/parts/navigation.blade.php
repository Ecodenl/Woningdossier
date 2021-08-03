{{-- Nav bar --}}
<div class="grid grid-flow-row grid-cols-3 items-center w-full bg-white h-12 px-5 xl:px-20 relative z-40 shadow-lg">
    <div>
        {{-- TODO: Check if this should be interchangable per cooperation --}}
        <i class="icon-hoomdossier"></i>
    </div>
    <div>
        {{-- White space --}}
    </div>
    <div class="flex flex-row justify-end space-x-4">
        <p>
            <a href="{{ route('cooperation.home') }}">
                @lang('default.start')
            </a>
        </p>
        <p>
            <a class="text-blue">
                @lang('cooperation/frontend/layouts.navigation.advise')
            </a>
        </p>
        {{-- TODO: This will be chat-alert only if there are actual messages --}}
        <i class="icon-md icon-chat-alert"></i>
        <div class="flex items-center">
            <i class="icon-md icon-account-circle mr-1"></i>
            <i class="icon-xs icon-arrow-down"></i>
        </div>
    </div>
</div>