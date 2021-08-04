{{-- Nav bar --}}
<div class="grid grid-flow-row grid-cols-3 items-center w-full bg-white h-12 px-5 xl:px-20 relative z-40 shadow-lg">
    <div class="flex flex-row flex-wrap justify-between items-center">
        {{-- TODO: Check if this should be interchangable per cooperation --}}
        <a href="{{ route('cooperation.welcome') }}">
            <i class="icon-hoomdossier"></i>
        </a>
        @auth
            @if (\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())
                <a href="{{route('cooperation.admin.stop-session')}}" class="btn btn-yellow">
                    @lang('cooperation/frontend/layouts.navbar.stop-session')
                </a>
            @endif
            {{-- only show the "back to cooperation button when the user is an admin without resident role AND we're on the settings page AND there's only one --}}
            @if(\App\Helpers\Hoomdossier::user()->can('access-admin') && !\App\Helpers\Hoomdossier::user()->hasRole('resident') && \App\Helpers\Hoomdossier::user()->getRoleNames()->count() <= 1 && !\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())
                <a href="{{ route('cooperation.admin.index') }}" class="btn btn-green">
                    @lang('cooperation/frontend/layouts.navbar.back-to-cooperation')
                </a>
            @endif
        @endauth
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
                @lang('cooperation/frontend/layouts.navbar.advise')
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