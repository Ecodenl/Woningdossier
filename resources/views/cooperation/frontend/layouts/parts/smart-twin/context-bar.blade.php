{{--
    The things the navigation itself has no room for, but that the user must be able to see at any
    scroll position: which role they are acting as, and — the important one — whose house they are
    filling in. Renders nothing at all for a resident with a single role, which is the common case.
--}}
@auth
    @php
        $building = \App\Helpers\HoomdossierSession::getBuilding(true);
        $isFillingForOther = Hoomdossier::user()->isFillingToolForOtherBuilding();
        $hasRoleSwitcher = ! $isFillingForOther
            && Hoomdossier::user()->getRoleNames()->count() > 1
            && \App\Helpers\HoomdossierSession::hasRole();
    @endphp

    @if($isFillingForOther || $hasRoleSwitcher)
        <div class="sticky top-0 flex flex-row flex-wrap justify-between items-center w-full bg-blue-100 border-b border-solid border-blue-500 border-opacity-25 px-5 xl:px-20 py-2 z-130">
            <div class="flex flex-row flex-wrap items-center space-x-4">
                @if($isFillingForOther && $building instanceof \App\Models\Building)
                    <p class="btn btn-purple mb-0">
                        @lang('cooperation/frontend/layouts.context-bar.building', [
                            'name' => $building->user->getFullName(),
                            'address' => "{$building->postal_code} - {$building->number} {$building->extension}",
                        ])
                    </p>
                    <a href="{{ route('cooperation.admin.stop-session') }}" class="btn btn-yellow mb-0">
                        @lang('cooperation/frontend/layouts.navbar.stop-session')
                    </a>
                @endif
            </div>

            <div class="flex flex-row flex-wrap items-center">
                @include('cooperation.layouts.parts.role-switcher')
            </div>
        </div>
    @endif
@endauth
