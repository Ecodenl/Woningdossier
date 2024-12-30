@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/buildings.show.header', [
        'name' => $user->getFullName(),
        'street-and-number' => $building->street.' '.$building->number.' '.$building->extension,
        'zipcode-and-city' => $building->postal_code.' '.$building->city,
        'municipality' => $building->municipality?->name ?? __('cooperation/admin/buildings.show.unknown-municipality'),
        'email' => $user->account->email,
        'phone-number' => $user->phone_number,
    ])
])

@section('content')
    @if(! $user->allowedAccess())
        <div class="w-full">
            <p class="font-bold">@lang('cooperation/admin/buildings.show.user-disallowed-access'):</p>
            <p class="text-red">(@lang('my-account.access.index.form.allow_access', ['cooperation' => \App\Helpers\HoomdossierSession::getCooperation(true)->name]))</p>
        </div>
    @endif
    <div class="flex flex-wrap w-full justify-between">
        <div>
            @can('delete-user', $user)
                <button type="button" id="delete-user" class="btn btn-red">
                    <span class="flex items-center">
                        @lang('cooperation/admin/buildings.show.delete-account.label')
                        <i class="icon-sm icon-trash-can ml-1"></i>
                    </span>
                </button>
            @endcan
            @can('access-building', $building)
                @if($scans->count() > 1)
                    @component('cooperation.layouts.components.dropdown', [
                        'label' => __('cooperation/admin/buildings.show.observe-building.label') . '<i class="icon-sm icon-show ml-1"></i>',
                        'class' => 'btn btn-green',
                    ])
                        @foreach($scans as $scan)
                            @php
                                $transShort = app(\App\Services\Models\ScanService::class)
                                    ->scan($scan)->forBuilding($building)->hasMadeScanProgress()
                                    ? 'home.start.buttons.continue' : 'home.start.buttons.start';
                            @endphp
                            <li>
                                <a class="in-text" href="{{route('cooperation.admin.tool.observe-tool-for-user', compact('building', 'scan'))}}">
                                    @lang($transShort, ['scan' => $scan->name])
                                </a>
                            </li>
                        @endforeach
                    @endcomponent
                @else
                    @foreach($scans as $scan)
                        <a class="btn btn-green" href="{{route('cooperation.admin.tool.observe-tool-for-user', compact('building', 'scan'))}}">
                            <span class="flex items-center">
                                @lang('cooperation/admin/buildings.show.observe-building.label')
                                <i class="icon-sm icon-show ml-1"></i>
                            </span>
                        </a>
                    @endforeach
                @endif
                {{-- TODO: This should be a policy --}}
                @if(Hoomdossier::user()->hasRoleAndIsCurrentRole('coach'))
                    @if($scans->count() > 1)
                        @component('cooperation.layouts.components.dropdown', [
                            'label' => __('cooperation/admin/buildings.show.fill-for-user.label') . '<i class="icon-sm icon-tools ml-1"></i>',
                            'class' => 'btn btn-yellow',
                        ])
                            @foreach($scans as $scan)
                                @php
                                    $transShort = app(\App\Services\Models\ScanService::class)
                                        ->scan($scan)->forBuilding($building)->hasMadeScanProgress()
                                        ? 'home.start.buttons.continue' : 'home.start.buttons.start';
                                @endphp
                                <li>
                                    <a class="in-text" href="{{route('cooperation.admin.tool.observe-tool-for-user', compact('building', 'scan'))}}">
                                        @lang($transShort, ['scan' => $scan->name])
                                    </a>
                                </li>
                            @endforeach
                        @endcomponent
                    @else
                        @foreach($scans as $scan)
                            <a class="btn btn-yellow" href="{{route('cooperation.admin.tool.fill-for-user', compact('building', 'scan'))}}">
                                @php
                                    $transShort = app(\App\Services\Models\ScanService::class)
                                        ->scan($scan)->forBuilding($building)->hasMadeScanProgress()
                                        ? 'home.start.buttons.continue' : 'home.start.buttons.start';
                                @endphp
                                <span class="flex items-center">
                                    @lang($transShort, ['scan' => $scan->name])
                                    <i class="icon-sm icon-tools ml-1"></i>
                                </span>
                            </a>
                        @endforeach
                    @endif
                @endif
            @endcan
            @can('edit', $building)
                <a href="{{route('cooperation.admin.buildings.edit', compact('building'))}}" id="edit-building" class="btn btn-blue">
                    <span class="flex items-center">
                        @lang('cooperation/admin/buildings.show.edit.label')
                        <i class="icon-sm icon-pencil ml-1"></i>
                    </span>
                </a>
            @endcan
        </div>
    </div>

    <div class="flex flex-wrap w-full sm:pad-x-6">
        {{-- status and appointment date --}}
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full sm:w-1/2',
            'label' => __('cooperation/admin/buildings.show.status.label'),
            'id' => 'building-status',
            'inputName' => "building.building_statuses.id",
            'withInputSource' => false,
        ])
            @component('cooperation.frontend.layouts.components.alpine-select')
                <select id="building-status" class="form-input hidden" autocomplete="off"
                        name="building[building_statuses][id]">
                    @foreach($statuses as $status)
                        <option value="{{$status->id}}"
                                @if($mostRecentStatus?->status_id == $status->id) selected data-current @endif
                        >
                            {{ $mostRecentStatus?->status_id == $status->id ? __('cooperation/admin/buildings.show.status.current') . $status->name : $status->name }}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent

        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full sm:w-1/2',
            'label' => __('cooperation/admin/buildings.show.appointment-date.label'),
            'id' => 'appointment-date',
            'inputName' => "building.building_statuses.appointment_date",
            'withInputSource' => false,
        ])
            @include('cooperation.layouts.parts.datepicker', [
                'mode' => 'datetime',
                'name' => 'building.building_statuses.appointment_date',
                'id' => 'appointment-date',
                'placeholder' => '', // No placeholder!
                'date' => $mostRecentStatus instanceof \App\Models\BuildingStatus && $mostRecentStatus->hasAppointmentDate() ? $mostRecentStatus->appointment_date : null,
            ])
        @endcomponent
    </div>

    <div class="flex flex-wrap w-full sm:pad-x-6">
        {{--coaches and role--}}
        @if($publicMessages->isNotEmpty())
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'w-full sm:w-1/2',
                'label' => __('cooperation/admin/buildings.show.associated-coach.label'),
                'id' => 'associated-coaches',
                'inputName' => "user.associated_coaches",
                'withInputSource' => false,
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select multiple id="associated-coaches" class="form-input hidden" name="user[associated_coaches]"
                            @if(Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) disabled @endif>
                        @foreach($coaches as $coach)
                            <option value="{{$coach->id}}"
                                    @if($coachesWithActiveBuildingCoachStatus->contains('coach_id', $coach->id)) selected @endif
                            >
                                {{$coach->getFullName()}}
                            </option>
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent
        @endif

        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full sm:w-1/2',
            'label' => __('cooperation/admin/buildings.show.role.label'),
            'id' => 'role-select',
            'inputName' => "user.roles",
            'withInputSource' => false,
        ])
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select multiple id="role-select" class="form-input hidden" name="user[roles]"
                        @cannot('editAny', $userCurrentRole) disabled @endcannot>
                    @foreach($roles as $role)
                        @can('view', [$role, Hoomdossier::user(), HoomdossierSession::getRole(true)])
                            <option value="{{$role->id}}"
                                    @cannot('delete', [$role, Hoomdossier::user(), HoomdossierSession::getRole(true), $user]) readonly @endcannot
                                    @if($user->hasRole($role)) selected @endif
                            >
                                {{$role->human_readable_name}}
                            </option>
                        @endcan
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
    </div>

    <div class="flex flex-wrap w-full">
        @can('create', [\App\Models\Media::class, HoomdossierSession::getInputSource(true), $building, MediaHelper::BUILDING_IMAGE])
            <livewire:cooperation.admin.buildings.uploader :building="$building" tag="{{ MediaHelper::BUILDING_IMAGE }}">
        @endcan
    </div>

    <div x-data="tabs()">
        <nav class="nav-tabs">
            <a x-bind="tab" data-tab="messages-intern">
                @lang('cooperation/admin/buildings.show.tabs.messages-intern.title')
            </a>

            @can('talk-to-resident', [$building])
                <a x-bind="tab" data-tab="messages-public" x-ref="main-tab" class="flex items-center">
                    @php $retrievesNotifications = $user->retrievesNotifications(\App\Models\NotificationType::PRIVATE_MESSAGE); @endphp
                    @component('cooperation.layouts.components.popover', [
                        'position' => 'top',
                        'trigger' => 'hover',
                    ])
                        @if($retrievesNotifications)
                            <i class="icon-sm icon-check-circle-purple mr-1"></i>
                        @else
                            <i class="w-3 h-3 icon-error-cross mr-1"></i>
                        @endif

                        @slot('body')
                            <p>
                                @lang('cooperation/admin/buildings.show.tabs.messages-public.user-notification.' . ($retrievesNotifications ? 'yes' : 'no'))
                            </p>
                        @endslot
                    @endcomponent

                    @lang('cooperation/admin/buildings.show.tabs.messages-public.title')
                </a>
            @endcan

            <a x-bind="tab" data-tab="building-notes">
                @lang('cooperation/admin/buildings.show.tabs.comments-on-building.title')
            </a>

            @if(Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                <a x-bind="tab"  data-tab="fill-in-history">
                    @lang('cooperation/admin/buildings.show.tabs.fill-in-history.title')
                </a>
            @endif

            <a x-bind="tab" data-tab="2fa">
                @lang('cooperation/admin/buildings.show.tabs.2fa.title')
            </a>

            @can('viewAny', [\App\Models\Media::class, HoomdossierSession::getInputSource(true), $building])
                <a x-bind="tab" data-tab="view-files" class="flex items-center">
                    @lang('cooperation/admin/buildings.show.view-files')
                    <i class="icon-sm icon-document-white ml-1"></i>
                </a>
            @endcan
        </nav>

        <div class="border border-t-0 border-blue/50 rounded-b-lg p-4">
            {{--messages intern (cooperation to cooperation --}}
            <div id="messages-intern" x-bind="container" data-tab="messages-intern">
                @include('cooperation.layouts.parts.message-box', [
                    'privateMessages' => $privateMessages,
                    'building' => $building,
                    'isPublic' => false,
                    'showParticipants' => false,
                    'url' => route('cooperation.admin.send-message'),
                ])
            </div>

            @can('talk-to-resident', [$building])
                {{--public messages / between the resident and cooperation--}}
                <div id="messages-public" x-bind="container" data-tab="messages-public">
                    @include('cooperation.layouts.parts.message-box', [
                        'privateMessages' => $publicMessages,
                        'building' => $building,
                        'isPublic' => true,
                        'showParticipants' => false,
                        'url' => route('cooperation.admin.send-message'),
                    ])
                </div>
            @endcan

            {{-- comments on the building, read only. --}}
            <div id="building-notes" x-bind="container" data-tab="building-notes" class="p-4">
                @foreach($buildingNotes as $buildingNote)
                    <p class="float-right">{{$buildingNote->created_at->format('Y-m-d H:i')}}</p>
                    <p>{{$buildingNote->note}}</p>
                    <hr>
                @endforeach

                <form action="{{route('cooperation.admin.building-notes.store')}}" method="POST">
                    @csrf
                    <input type="hidden" name="building[id]" value="{{$building->id}}">

                    @component('cooperation.frontend.layouts.components.form-group', [
                        'label' => __('cooperation/admin/buildings.show.tabs.comments-on-building.note'),
                        'id' => 'building-note',
                        'inputName' => "building.note",
                        'withInputSource' => false,
                    ])
                        <textarea id="building-note" name="building[note]"
                                  class="form-input"
                        >{{old('building.note')}}</textarea>
                    @endcomponent
                    <button type="submit" class="btn btn-outline-green">
                        @lang('cooperation/admin/buildings.show.tabs.comments-on-building.save')
                    </button>
                </form>
            </div>

            {{-- Fill in history ?? the log --}}
            @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                <div id="fill-in-history" x-bind="container" data-tab="fill-in-history" class="data-table">
                    <table id="log-table"
                           class="table fancy-table">
                        <thead>
                            <tr>
                                <th>@lang('cooperation/admin/buildings.show.tabs.fill-in-history.table.columns.happened-on')</th>
                                <th>@lang('cooperation/admin/buildings.show.tabs.fill-in-history.table.columns.message')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php /** @var \App\Models\Log $log */ @endphp
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        {{$log->created_at->format('d-m-Y H:i')}}
                                    </td>
                                    <td>
                                        {{$log->message}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div id="2fa" x-bind="container" data-tab="2fa" class="p-4">
                @if($user->account->hasEnabledTwoFactorAuthentication())
                    @component('cooperation.layouts.components.alert', ['color' => 'green', 'dismissible' => false])
                        @lang('cooperation/admin/buildings.show.tabs.2fa.status.active.title')
                    @endcomponent

                    @can('disableTwoFactor', $user->account)
                        <form action="{{route('cooperation.admin.cooperation.accounts.disable-2fa')}}" method="POST">
                            @csrf
                            <input type="hidden" name="accounts[id]" value="{{$user->account_id}}">
                            <button type="submit" class="btn btn-red">
                                @lang('cooperation/admin/buildings.show.tabs.2fa.status.active.button')
                            </button>
                        </form>
                    @endcan
                @else
                    @component('cooperation.layouts.components.alert', ['color' => 'blue-900', 'dismissible' => false])
                        @lang('cooperation/admin/buildings.show.tabs.2fa.status.inactive.title')
                    @endcomponent
                @endif
            </div>

            @can('viewAny', [\App\Models\Media::class, HoomdossierSession::getInputSource(true), $building])
                <div id="view-files" x-bind="container" data-tab="view-files" class="p-4">
                    <livewire:cooperation.frontend.tool.simple-scan.my-plan.uploader :building="$building"/>
                </div>
            @endcan
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        // so when a user changed the appointment date and does not want to save it, we change it back to the value we got onload.
        const originalAppointmentDate = @if($mostRecentStatus instanceof \App\Models\BuildingStatus && $mostRecentStatus->hasAppointmentDate()) '{{$mostRecentStatus->appointment_date->format('Y-m-d H:i')}}' @else '' @endif;
        const buildingOwnerId = @js($building->id);
        const userId = @js($user->id);

        function performFetch(url, body, redirect) {
            fetchRequest(url, 'POST', body)
                .then((response) => redirect ? location.href = redirect : location.reload());
        }

        document.addEventListener('DOMContentLoaded', () => {
            // delete the current user
            document.getElementById('delete-user')?.addEventListener('click', function () {
                if (confirm('@lang('cooperation/admin/buildings.show.delete-user')')) {
                    performFetch('{{route('cooperation.admin.users.destroy')}}', {
                        user_id: userId,
                        _method: 'DELETE'
                    }, '{{route('cooperation.admin.users.index')}}');
                }
            });

            const associatedCoachesSelect = document.getElementById('associated-coaches');
            const associatedCoaches = Array.from(associatedCoachesSelect.selectedOptions).map((option) => option.value);

            const roleSelect = document.getElementById('role-select');
            const currentRoles = Array.from(roleSelect.selectedOptions).map((option) => option.value);

            // Change building status
            document.getElementById('building-status').addEventListener('change', function () {
                if (confirm('@lang('cooperation/admin/buildings.show.set-status')')) {
                    performFetch('{{ route('cooperation.admin.building-status.set-status') }}', {
                        building_id: buildingOwnerId,
                        status_id: this.value,
                    });
                } else {
                    // Reset status
                    this.value = this.querySelector('option[data-current]').value;
                    this.alpineSelect.updateSelectedValues();
                }
            });

            // Appointment date
            document.querySelector('.datepicker').addEventListener('datepicker-closed', function () {
                const date = this.querySelector('.datepicker-value').value;

                if (date !== originalAppointmentDate) {
                    let confirmMessage = "@lang('cooperation/admin/buildings.show.set-empty-appointment-date')";

                    if (date.length > 0) {
                        confirmMessage = "@lang('cooperation/admin/buildings.show.set-appointment-date')"
                    }

                    if (confirm(confirmMessage)) {
                        performFetch('{{ route('cooperation.admin.building-status.set-appointment-date') }}', {
                            building_id: buildingOwnerId,
                            appointment_date: date,
                        });
                    } else {
                        // If the user does not want to set / change the appointment date,
                        // we set the date back to the one we got onload.
                        this.datepicker.setDate(originalAppointmentDate);
                    }
                }
            });

            @if(! Hoomdossier::user()->hasRoleAndIsCurrentRole('coach'))
            // Associated coaches
            associatedCoachesSelect.addEventListener('change', function (event) {
                // If length is greater, a value was removed, otherwise added
                if (associatedCoaches.length > this.selectedOptions.length) {
                    let removedOption = null;
                    const currentOptions = Array.from(this.selectedOptions).map((option) => option.value);
                    associatedCoaches.forEach((value) => removedOption = ! removedOption && ! currentOptions.includes(value) ? value : removedOption);

                    if (confirm('@lang('cooperation/admin/buildings.show.revoke-access')')) {
                        performFetch('{{ route('cooperation.messages.participants.revoke-access') }}', {
                            building_owner_id: buildingOwnerId,
                            user_id: removedOption,
                        });
                    } else {
                        this.alpineSelect.updateValue(removedOption);
                    }
                } else {
                    let newOption = null;
                    Array.from(this.selectedOptions).forEach((option) => newOption = ! newOption && ! associatedCoaches.includes(option.value) ? option.value : newOption);

                    if (confirm('@lang('cooperation/admin/buildings.show.add-with-building-access')')) {
                        performFetch('{{ route('cooperation.messages.participants.add-with-building-access') }}', {
                            building_id: buildingOwnerId,
                            user_id: newOption,
                        });
                    } else {
                        this.alpineSelect.updateValue(newOption);
                    }
                }
            });
            @endif

            @can('editAny', $userCurrentRole)
            // User roles
            roleSelect.addEventListener('change', function (event) {
                // If length is greater, a value was removed, otherwise added
                if (currentRoles.length > this.selectedOptions.length) {
                    let removedOption = null;
                    const currentOptions = Array.from(this.selectedOptions).map((option) => option.value);
                    currentRoles.forEach((value) => removedOption = ! removedOption && ! currentOptions.includes(value) ? value : removedOption);

                    if (confirm('@lang('cooperation/admin/buildings.show.remove-role')')) {
                        performFetch('{{ route('cooperation.admin.roles.remove-role') }}', {
                            role_id: removedOption,
                            user_id: userId,
                        });
                    } else {
                        this.alpineSelect.updateValue(removedOption);
                    }
                } else {
                    let newOption = null;
                    Array.from(this.selectedOptions).forEach((option) => newOption = ! newOption && ! currentRoles.includes(option.value) ? option.value : newOption);

                    if (confirm('@lang('cooperation/admin/buildings.show.give-role')')) {
                        performFetch('{{ route('cooperation.admin.roles.assign-role') }}', {
                            role_id: newOption,
                            user_id: userId,
                        });
                    } else {
                        this.alpineSelect.updateValue(newOption);
                    }
                }
            });
            @endcan
        });

        window.addEventListener('tab-switched', () => {
            setChatScroll();
        });
    </script>
@endpush