{{--
    The one thing the navigation has no room for but that must stay visible at any scroll position:
    whose house you are filling in. Renders nothing at all for a resident working on their own
    building, which is the common case.

    Switching roles is not here. It is a link to the cooperation environment in the navigation
    instead, which lands on the role picker anyway.
--}}
@auth
    @php
        $building = \App\Helpers\HoomdossierSession::getBuilding(true);
    @endphp

    @if(Hoomdossier::user()->isFillingToolForOtherBuilding() && $building instanceof \App\Models\Building)
        <div class="sticky top-0 flex flex-row flex-wrap items-center w-full bg-blue-100 border-b border-solid border-blue-500 border-opacity-25 px-5 xl:px-20 py-2 space-x-4 z-130">
            <p class="btn btn-purple mb-0">
                @lang('cooperation/frontend/layouts.context-bar.building', [
                    'name' => $building->user->getFullName(),
                    'address' => "{$building->postal_code} - {$building->number} {$building->extension}",
                ])
            </p>
            <a href="{{ route('cooperation.admin.stop-session') }}" class="btn btn-yellow mb-0">
                @lang('cooperation/frontend/layouts.navbar.stop-session')
            </a>
        </div>
    @endif
@endauth
