{{--
    Main navigation. $scan and $building come from the NavbarComposer, which is bound to this view in
    the ViewServiceProvider.
--}}
@auth
    @php
        $currentRoute = Route::currentRouteName();
        $isFillingForOther = Hoomdossier::user()->isFillingToolForOtherBuilding();

        $woningdossierUrl = route('cooperation.frontend.tool.scan.redirect', compact('scan'));
        $filesUrl = route('cooperation.frontend.tool.simple-scan.my-plan.media', compact('scan'));
    @endphp

    <div class="flex flex-row flex-wrap justify-between items-center w-full bg-gray-400 px-5 xl:px-20 h-14 relative z-140">
        <div class="flex flex-row items-center space-x-8 xl:space-x-12">
            {{-- A user filling in for someone else gets bounced off the dashboard by middleware, so
                 we don't offer it to them. --}}
            @if(! $isFillingForOther)
                <a href="{{ route('cooperation.home') }}"
                   class="nav-item @if(RouteLogic::inDashboard($currentRoute)) is-active @endif">
                    @lang('cooperation/frontend/layouts.nav.dashboard')
                </a>
            @endif

            <a href="{{ $woningdossierUrl }}"
               class="nav-item @if(RouteLogic::inWoningdossier($currentRoute)) is-active @endif">
                @lang('cooperation/frontend/layouts.nav.woningdossier')
            </a>

            {{-- The two counted items carry their own permission checks; they differ per item. --}}
            <livewire:cooperation.frontend.layouts.parts.smart-twin.nav-counters :fileUrl="$filesUrl"/>
        </div>

        @if(! $isFillingForOther)
            <div class="flex flex-row items-center space-x-8 xl:space-x-12">
                {{-- More than one role means there is something to switch to. The admin index shows
                     the role picker when a user has several, so linking there beats carrying a
                     switcher around in the navigation. --}}
                @if(Hoomdossier::user()->getRoleNames()->count() > 1)
                    <a href="{{ route('cooperation.admin.index') }}" class="nav-item">
                        @lang('cooperation/frontend/layouts.nav.cooperation')
                    </a>
                @endif

                <a href="{{ route('cooperation.my-account.index', compact('cooperation')) }}"
                   class="nav-item @if(RouteLogic::inMyAccount($currentRoute)) is-active @endif">
                    @lang('cooperation/frontend/layouts.nav.my-account')
                </a>
            </div>
        @endif
    </div>
@endauth
