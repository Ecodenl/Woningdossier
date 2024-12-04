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
    <input type="hidden" name="building[id]" value="{{$building->id}}">
    <input type="hidden" id="cooperation-id" value="{{HoomdossierSession::getCooperation()}}">

    <input type="hidden" name="user[id]" value="{{$user->id}}">

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
        <div>
            @can('viewAny', [\App\Models\Media::class, HoomdossierSession::getInputSource(true), $building])
                <button role="button" class="btn btn-outline-blue" id="view-files">
                    @lang('cooperation/admin/buildings.show.view-files')
                    <i class="icon-sm icon-document"></i>
                </button>
            @endcan
        </div>
    </div>

    <div class="flex flex-wrap w-full sm:pad-x-6">
        {{-- status and appointment date --}}
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full sm:w-1/2',
            'label' => __('cooperation/admin/buildings.show.status.label'),
            'id' => 'building-coach-status',
            'inputName' => "building.building_statuses.id",
            'withInputSource' => false,
        ])
            @component('cooperation.frontend.layouts.components.alpine-select')
                <select id="building-coach-status" class="form-input hidden" autocomplete="off"
                        name="building[building_statuses][id]">
                    @foreach($statuses as $status)
                        <option {{$mostRecentStatus?->status_id == $status->id ? 'selected="selected"' : ''}} value="{{$status->id}}">
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
            <input autocomplete="off" id="appointment-date" name="building[building_statuses][appointment_date]"
                   type="text" class="form-input with-append"
                   @if($mostRecentStatus instanceof \App\Models\BuildingStatus && $mostRecentStatus->hasAppointmentDate()) value=" {{$mostRecentStatus->appointment_date->format('d-m-Y H:i')}}" @endif
            />

            <div class="input-group-append cursor-pointer">
                <i class="icon-md icon-calendar"></i>
            </div>
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
                @component('cooperation.frontend.layouts.components.alpine-select')
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
            @component('cooperation.frontend.layouts.components.alpine-select')
                <select multiple id="role-select" class="form-input hidden" name="user[roles]"
                        @cannot('editAny', $userCurrentRole) disabled @endcannot>
                    @foreach($roles as $role)
                        @can('view', [$role, Hoomdossier::user(), HoomdossierSession::getRole(true)])
                            <option value="{{$role->id}}"
                                    @cannot('delete',  [$role, Hoomdossier::user(), HoomdossierSession::getRole(true), $building->user]) disabled @endcannot
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
                            <i class="icon-sm icon-check-circle-purple"></i>
                        @else
                            <i class="w-3 h-3 icon-error-cross"></i>
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
                <a data-toggle="tab" id="trigger-fill-in-history-tab" href="#fill-in-history">
                    @lang('cooperation/admin/buildings.show.tabs.fill-in-history.title')
                </a>
            @endif

            <a x-bind="tab" data-tab="2fa">
                @lang('cooperation/admin/buildings.show.tabs.2fa.title')
            </a>
        </nav>

        <div class="border border-t-0 border-blue border-opacity-50 rounded-b-lg">
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

            <div id="2fa" x-bind="container" data-tab="2fa" class="p-4">
                @if($building->user->account->hasEnabledTwoFactorAuthentication())
                    @component('cooperation.layouts.components.alert', ['color' => 'green', 'dismissible' => false])
                        @lang('cooperation/admin/buildings.show.tabs.2fa.status.active.title')
                    @endcomponent

                    @can('disableTwoFactor', $building->user->account)
                        <form action="{{route('cooperation.admin.cooperation.accounts.disable-2fa')}}" method="POST">
                            @csrf
                            <input type="hidden" name="accounts[id]" value="{{$building->user->account_id}}">
                            <button type="submit" class="btn btn-red">
                                @lang('cooperation/admin/buildings.show.tabs.2fa.status.active.button')
                            </button>
                        </form>
                    @endcan
                @else
                    @component('cooperation.layouts.components.alert', ['color' => 'blue-800', 'dismissible' => false])
                        @lang('cooperation/admin/buildings.show.tabs.2fa.status.inactive.title')
                    @endcomponent
                @endif
            </div>

            {{-- Fill in history ?? the log --}}
            @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                <div id="fill-in-history" x-bind="container" data-tab="fill-in-history">
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
        </div>
    </div>

    @can('viewAny', [\App\Models\Media::class, HoomdossierSession::getInputSource(true), $building])


{{--        <div id="files-modal" class="modal fade" role="dialog">--}}
{{--            <div class="modal-dialog" style="height: 100vh; width: 100vw; margin: 0;">--}}

{{--                <!-- Modal content-->--}}
{{--                <div class="modal-content" style="height: 100%; width: 100%; overflow: hidden;">--}}
{{--                    <div class="modal-header">--}}
{{--                        <button type="button" class="close" data-dismiss="modal">&times;</button>--}}
{{--                        <h4 class="modal-title">--}}
{{--                            @lang('cooperation/admin/buildings.show.view-files')--}}
{{--                        </h4>--}}
{{--                    </div>--}}
{{--                    <div class="modal-body" style="margin: 0; padding: 0; height: 100%;">--}}
{{--                        <iframe src="{{ route('cooperation.frontend.tool.simple-scan.my-plan.media', compact('building', 'scan')) . "?iframe=1" }}"--}}
{{--                                style="border: none; width: 100%; height: 100%;"></iframe>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    @endcan
@endsection

@push('js')
    <script type="module">
        // so when a user changed the appointment date and does not want to save it, we change it back to the value we got onload.
        let originalAppointmentDate = @if($mostRecentStatus instanceof \App\Models\BuildingStatus && $mostRecentStatus->hasAppointmentDate()) '{{$mostRecentStatus->appointment_date->format('d-m-Y H:i')}}' @else '' @endif;

        document.addEventListener('DOMContentLoaded', function () {

            // get some basic information
            let buildingOwnerId = $('input[name=building\\[id\\]]').val();
            let userId = $('input[name=user\\[id\\]]').val();
            let cooperationId = $('#cooperation-id').val();

            let appointmentDate = $('#appointment-date');

            $('[data-toggle="tooltip"]').tooltip();

            scrollChatToMostRecentMessage();
            onFormSubmitAddFragmentToRequest();


            $('.nav-tabs .active a').trigger('shown.bs.tab');

            let currentDate = new Date();
            currentDate.setDate(currentDate.getDate() - 1);

            appointmentDate.datetimepicker({
                showTodayButton: true,
                allowInputToggle: true,
                locale: 'nl',
                // format: 'L',
                showClear: true,
            }).on('dp.hide', function (event) {
                // This way the right events get triggered so we will always get a nice formatted date
                appointmentDate.find('input').blur();

                // Queue the confirm so the DOM is properly updated for the browsers which seem to ignore the blur.
                setTimeout(() => {
                    let date = appointmentDate.find('input').val();

                    if (date !== originalAppointmentDate) {
                        let confirmMessage = "@lang('cooperation/admin/buildings.show.set-empty-appointment-date')";

                        if (date.length > 0) {
                            confirmMessage = "@lang('cooperation/admin/buildings.show.set-appointment-date')"
                        }

                        if (confirm(confirmMessage)) {
                            $.ajax({
                                method: 'POST',
                                url: '{{route('cooperation.admin.building-status.set-appointment-date')}}',
                                data: {
                                    building_id: buildingOwnerId,
                                    appointment_date: date
                                },
                            }).fail(function (response) {
                                appointmentDate.find('input').get(0).addError('Invalid format');
                            }).done(function () {
                                location.reload();
                            })
                        } else {
                            // if the user does not want to set / change the appointment date
                            // we set the date back to the one we got onload.
                            appointmentDate.find('input').val(originalAppointmentDate);
                        }
                    }
                });
            });

            // delete the current user
            $('#delete-user').click(function () {
                if (confirm('@lang('cooperation/admin/buildings.show.delete-user')')) {

                    $.ajax({
                        url: '{{route('cooperation.admin.users.destroy')}}',
                        method: 'POST',
                        data: {
                            user_id: userId,
                            _method: 'DELETE'
                        }
                    }).done(function () {
                        window.location.href = '{{route('cooperation.admin.users.index')}}'
                    })
                }
            });

            $('#building-status').select2({}).on('select2:selecting', function (event) {
                let statusToSelect = $(event.params.args.data.element);

                if (confirm('@lang('cooperation/admin/buildings.show.set-status')')) {
                    $.ajax({
                        method: 'POST',
                        url: '{{route('cooperation.admin.building-status.set-status')}}',
                        data: {
                            building_id: buildingOwnerId,
                            status_id: statusToSelect.val(),
                        }
                    }).done(function () {
                        location.reload();
                    })
                } else {
                    event.preventDefault();
                    return false;
                }
            });
            $('#associated-coaches').select2({
                templateSelection: function (tag, container) {
                    let option = $('#associated-coaches option[value="' + tag.id + '"]');
                    if (option.attr('locked')) {
                        $(container).addClass('select2-locked-tag');
                        tag.locked = true
                    }

                    return tag.text;
                }
            }).on('select2:unselecting', function (event) {
                let optionToUnselect = $(event.params.args.data.element);

                // check if the option is locked
                if (typeof optionToUnselect.attr('locked') === "undefined") {
                    if (confirm('@lang('cooperation/admin/buildings.show.revoke-access')')) {
                        $.ajax({
                            url: '{{route('cooperation.messages.participants.revoke-access')}}',
                            method: 'POST',
                            data: {
                                user_id: optionToUnselect.val(),
                                building_owner_id: buildingOwnerId
                            }
                        }).done(function () {
                            // just reload the page
                            location.reload();
                        });
                    } else {
                        event.preventDefault();
                        return false;
                    }
                } else {
                    event.preventDefault();
                    return false;
                }
            }).on('select2:selecting', function (event) {
                let optionToSelect = $(event.params.args.data.element);

                if (confirm('@lang('cooperation/admin/buildings.show.add-with-building-access')')) {
                    $.ajax({
                        url: '{{route('cooperation.messages.participants.add-with-building-access')}}',
                        method: 'POST',
                        data: {
                            user_id: optionToSelect.val(),
                            building_id: buildingOwnerId
                        }
                    }).done(function () {
                        // just reload the page
                        location.reload();
                    });
                } else {
                    event.preventDefault();
                    return false;
                }
            });

            $('#role-select').select2({
                templateSelection: function (tag, container) {
                    let option = $('#role-select option[value="' + tag.id + '"]');
                    if (option.attr('locked')) {
                        $(container).addClass('select2-locked-tag');
                        tag.locked = true
                    }

                    return tag.text;
                }
            })
                .on('select2:selecting', function (event) {
                    let roleToSelect = $(event.params.args.data.element);

                    if (confirm('@lang('cooperation/admin/buildings.show.give-role')')) {
                        $.ajax({
                            url: '{{route('cooperation.admin.roles.assign-role')}}',
                            method: 'POST',
                            data: {
                                role_id: roleToSelect.val(),
                                user_id: userId,
                                cooperation_id: cooperationId
                            }
                        }).done(function () {
                            // just reload the page
                            location.reload();
                        });
                    } else {
                        event.preventDefault();
                        return false;
                    }
                })
                .on('select2:unselecting', function (event) {
                    let roleToUnselect = $(event.params.args.data.element);

                    if (confirm('@lang('cooperation/admin/buildings.show.remove-role')')) {
                        $.ajax({
                            url: '{{route('cooperation.admin.roles.remove-role')}}',
                            method: 'POST',
                            data: {
                                role_id: roleToUnselect.val(),
                                user_id: userId
                            }
                        }).done(function () {
                            // just reload the page
                            location.reload();
                        });
                    } else {
                        event.preventDefault();
                        return false;
                    }
                });
        });



        function scrollChatToMostRecentMessage() {
            $('.nav-tabs a').on('shown.bs.tab', function () {

                let tabId = $(this).attr('href');
                let tab = $(tabId);
                let chat = tab.find('.panel-chat-body')[0];

                if (typeof chat !== "undefined") {
                    chat.scrollTop = chat.scrollHeight - chat.clientHeight;

                    let isChatPublic = tab.find('[name=is_public]').val();
                    let buildingId = tab.find('[name=building_id]').val();

                    $.ajax({
                        url: '{{route('cooperation.messages.participants.set-read')}}',
                        method: 'post',
                        data: {
                            is_public: isChatPublic,
                            building_id: buildingId
                        },
                        success: function () {
                            updateTotalUnreadMessageCount();
                        }
                    })
                }
            });
        }

        function onFormSubmitAddFragmentToRequest()
        {
            $('form').submit(function (event) {
                $(this).append($('<input>', {
                    type: 'hidden',
                    name: 'fragment',
                    value: $(this).parents('.tab-pane').prop('id'),
                }));
            });
        }

    </script>
@endpush