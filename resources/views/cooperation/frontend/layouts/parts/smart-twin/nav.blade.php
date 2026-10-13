{{--
    Main navigation. $scan and $building come from the NavbarComposer, which is bound to this view in
    the ViewServiceProvider.
--}}
@auth
    @php
        $currentRoute = Route::currentRouteName();
        $isFillingForOther = Hoomdossier::user()->isFillingToolForOtherBuilding();

        // Not the scan.redirect route: for a simple scan that controller sends you to the dashboard,
        // which is where you just came from. This is the same resolution the dashboard's own
        // start/continue button uses, so both drop you where you left off.
        $woningdossierUrl = $building instanceof \App\Models\Building
            ? \App\Services\Scans\ScanFlowService::init($scan, $building, $masterInputSource)->resolveInitialUrl()
            : route('cooperation.home');

        $filesUrl = route('cooperation.frontend.tool.simple-scan.my-plan.media', compact('scan'));
    @endphp

    <div class="flex flex-row flex-wrap justify-between items-center w-full bg-gray-400 px-5 xl:px-20 h-14 relative z-130">
        <div class="flex flex-row items-center space-x-8 xl:space-x-12">
            {{-- A user filling in for someone else gets bounced off the dashboard by middleware, so
                 we don't offer it to them. --}}
            @if(! $isFillingForOther)
                <a href="{{ route('cooperation.home') }}"
                   class="nav-item @if(RouteLogic::inDashboard($currentRoute)) is-active @endif">
                    @lang('cooperation/frontend/layouts.nav.dashboard')
                </a>
            @endif

            {{-- Left out while a building with both scans still has to pick one on the dashboard. --}}
            @if($showWoningdossier)
                <a href="{{ $woningdossierUrl }}"
                   class="nav-item @if(RouteLogic::inWoningdossier($currentRoute)) is-active @endif">
                    @lang('cooperation/frontend/layouts.nav.woningdossier')
                </a>
            @endif

            {{-- The two counted items carry their own permission checks; they differ per item. --}}
            <livewire:cooperation.frontend.layouts.parts.smart-twin.nav-counters :fileUrl="$filesUrl"/>
        </div>

        @if(! $isFillingForOther)
            {{-- No link to switch roles: the logo takes anyone with a management role to the admin
                 index, which shows the role picker when there are several. --}}
            <div class="flex flex-row items-center space-x-8 xl:space-x-12">
                @component('cooperation.layouts.components.dropdown', [
                    'label' => __('cooperation/frontend/layouts.nav.my-account'),
                    'class' => 'nav-item' . (RouteLogic::inMyAccount($currentRoute) ? ' is-active' : ''),
                    'menuClass' => 'nav-dropdown',
                ])
                    @include('cooperation.frontend.layouts.parts.account-dropdown-items')
                @endcomponent
            </div>
        @endif
    </div>
@endauth
