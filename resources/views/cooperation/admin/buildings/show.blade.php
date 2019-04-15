@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.users.show.header', [
                'name' => $userExists ? $user->getFullName() : '-',
                'street-and-number' => $building->street.' '.$building->number,
                'zipcode-and-city' => $building->postal_code.' '.$building->city,
                'email' => $userExists ? $user->email : ''
            ])
        </div>

        <input type="hidden" name="building[id]" value="{{$building->id}}">
        @if($userExists)
            <input type="hidden" name="user[id]" value="{{$user->id}}">
        @endif
        <div class="panel-body">
            {{--delete a pipo--}}
            @if($userExists)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group">
                            @can('delete-user')
                                {{--<button type="button" id="delete-user" class="btn btn-danger">--}}
{{--                                    @lang('woningdossier.cooperation.admin.users.show.delete-account.label')--}}
{{--                                    @lang('woningdossier.cooperation.admin.users.show.delete-account.button')--}}
                                {{--</button>--}}
                            @endcan
                            @can('access-building', $building->id)
                                @can('user-access-building', $building->id)
                                    <a href="{{route('cooperation.admin.tool.observe-tool-for-user', ['buildingId' => $building->id])}}"
                                       id="observe-building" class="btn btn-primary">
                                        @lang('woningdossier.cooperation.admin.users.show.observe-building.label')
                                        @lang('woningdossier.cooperation.admin.users.show.observe-building.button')
                                    </a>
                                    @if(Auth::user()->hasRoleAndIsCurrentRole('coach'))
                                        <a href="{{route('cooperation.admin.tool.fill-for-user', ['buildingId' => $building->id])}}"
                                           id="edit-building" class="btn btn-warning">
                                            @lang('woningdossier.cooperation.admin.coach.buildings.show.fill-for-user.label')
                                            @lang('woningdossier.cooperation.admin.coach.buildings.show.fill-for-user.button')
                                        </a>
                                    @endif
                                @endcan
                            @endcan
                        </div>
                    </div>
                </div>
            @endif
            {{--status and appointment date--}}
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="building-coach-status">@lang('woningdossier.cooperation.admin.users.show.status.label')</label>
                        <select autocomplete="off" class="form-control" name="user[building_coach_status][status]"
                                id="building-coach-status">
                            {{--the user got a coach connected and the building is active--}}
                            @if($mostRecentBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus && $building->isActive())
                                <option disabled selected value="">
                                    @lang('woningdossier.cooperation.admin.users.show.status.current')
                                    {{\App\Models\BuildingCoachStatus::getTranslationForStatus($mostRecentBuildingCoachStatus->status)}}
                                </option>
                                {{--the user does not have a coach connected but the building is active--}}
                            @elseif(!$mostRecentBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus && $building->isActive())
                                {{--The user had some sort of message history so, pending.--}}
                                @if($publicMessages->isNotEmpty())
                                    <option disabled selected>
                                        @lang('woningdossier.cooperation.admin.users.show.status.current')
                                        {{\App\Models\BuildingCoachStatus::getTranslationForStatus(\App\Models\BuildingCoachStatus::STATUS_PENDING)}}
                                    </option>
                                    {{--The user has no message history--}}
                                @else
                                    <option disabled selected>
                                        @lang('woningdossier.cooperation.admin.users.show.status.current')
                                        {{\App\Models\BuildingCoachStatus::getTranslationForStatus(\App\Models\BuildingCoachStatus::STATUS_ACTIVE)}}
                                    </option>
                                @endif
                                {{--The user has no building coach status and his building is set to inactive--}}
                            @else
                                <option @if($building->isNotActive()) disabled selected @endif>
                                    @lang('woningdossier.cooperation.admin.users.show.status.current')
                                    {{\App\Models\Building::getTranslationForStatus(\App\Models\Building::STATUS_IS_NOT_ACTIVE)}}
                                </option>
                            @endif
                            {{--
                                If there is no active coach connected to the building, then there is no point in setting these statuses
                                and, it also cant be set since there arent ant coaches to give a status.
                            --}}
                            @if($coachesWithActiveBuildingCoachStatus->isNotEmpty())
                                @foreach($manageableStatuses as $buildingCoachStatusKey => $buildingCoachStatusName)
                                    <?php
                                    // the if logic
                                    $manageableStatusIsExecuted = $buildingCoachStatusKey == \App\Models\BuildingCoachStatus::STATUS_EXECUTED;
                                    $hasAppointmentThatIsToday = $mostRecentBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus && $mostRecentBuildingCoachStatus->hasAppointmentDate() && $mostRecentBuildingCoachStatus->appointment_date->isToday();
                                    $mostRecentBuildingCoachStatusExists = $mostRecentBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus;

                                    if ($mostRecentBuildingCoachStatusExists && $mostRecentBuildingCoachStatus->hasAppointmentDate() && $mostRecentBuildingCoachStatus->status != \App\Models\BuildingCoachStatus::STATUS_EXECUTED) {
                                        // check if the appointment day is past.
                                        $appointmentDayDateIsPast = !$mostRecentBuildingCoachStatus->appointment_date->lessThan(\Carbon\Carbon::now()->format('Y-m-d'));
                                    }
                                    // if there is an appointment date then it isn't allowed to change the status
                                    // but if that day is today and the manageable status is executed, then the coach may change it.
                                    // else we just want to show all the manageable statuses
                                    ?>
                                    @if($mostRecentBuildingCoachStatusExists && $mostRecentBuildingCoachStatus->hasAppointmentDate() && (isset($appointmentDayDateIsPast) && $appointmentDayDateIsPast == true))
                                        @if($hasAppointmentThatIsToday && $manageableStatusIsExecuted)
                                            <option value="{{$buildingCoachStatusKey}}">{{$buildingCoachStatusName}}</option>
                                        @endif
                                    @else
                                        <option value="{{$buildingCoachStatusKey}}">{{$buildingCoachStatusName}}</option>
                                    @endif

                                @endforeach
                            @endif

                            @if($building->isActive())
                                {{--This status can ALWAYS be choosen.--}}
                                <option value="{{\App\Models\Building::STATUS_IS_NOT_ACTIVE}}">
                                    {{\App\Models\Building::getTranslationForStatus(\App\Models\Building::STATUS_IS_NOT_ACTIVE)}}
                                </option>
                            @else
                                <option value="{{\App\Models\Building::STATUS_IS_ACTIVE}}">
                                    {{\App\Models\Building::getTranslationForStatus(\App\Models\Building::STATUS_IS_ACTIVE)}}
                                </option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="appointment-date">@lang('woningdossier.cooperation.admin.users.show.appointment-date.label')</label>
                        <div class='input-group date' id="appointment-date">
                            <?php $hasCoachStatusAndAppointmentIsNotNull = $mostRecentBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus && $mostRecentBuildingCoachStatus->hasAppointmentDate(); ?>
                            <input autocomplete="off"
                                   @if($userDoesNotExist || $coachesWithActiveBuildingCoachStatus->isEmpty() || $building->isNotActive())
                                   disabled
                                   @endif
                                   id="appointment-date" name="user[building_coach_status][appointment_date]"
                                   type='text' class="form-control"
                                   @if($hasCoachStatusAndAppointmentIsNotNull)
                                   value=" {{$mostRecentBuildingCoachStatus->appointment_date->format('d-m-Y')}}"
                                    @endif
                            />


                            <span class="input-group-addon">
                               <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            {{--coaches and role--}}
            <div class="row">
                @can('access-building', $building->id)
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="associated-coaches">@lang('woningdossier.cooperation.admin.users.show.associated-coach.label')</label>
                            <select @if(Auth::user()->hasRoleAndIsCurrentRole('coach')) disabled
                                    @endif name="user[associated_coaches]" id="associated-coaches" class="form-control"
                                    multiple="multiple">
                                    @foreach($coaches as $coach)
                                    <?php $coachBuildingStatus = $coachesWithActiveBuildingCoachStatus->where('coach_id', $coach->id) instanceof stdClass ?>
                                    <option
                                            @if($coachesWithActiveBuildingCoachStatus->contains('coach_id', $coach->id))
                                            selected="selected"
                                            @endif value="{{$coach->id}}">{{$coach->getFullName()}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endcan
                @if($userExists)
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="role-select">@lang('woningdossier.cooperation.admin.users.show.role.label')</label>
                            <select @if(Auth::user()->hasRoleAndIsCurrentRole('coach')) disabled
                                    @endif class="form-control" name="user[roles]" id="role-select" multiple="multiple">
                                @foreach($roles as $role)
                                    <option @if($user->hasNotMultipleRoles()) locked="locked"
                                            @endif @if($user->hasRole($role)) selected="selected"
                                            @endif value="{{$role->id}}">
                                        {{$role->human_readable_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <ul class="nav nav-tabs">

            <li class="active">
                <a data-toggle="tab" href="#messages-intern">
                    @lang('woningdossier.cooperation.admin.users.show.tabs.messages-intern.title')
                </a>
            </li>
            @can('access-building', $building->id)
                <li>
                    <a data-toggle="tab" href="#messages-public">
                        @lang('woningdossier.cooperation.admin.users.show.tabs.messages-public.title')
                    </a>
                </li>
            @endcan
            <li>
                <a data-toggle="tab" href="#comments-on-building">
                    @lang('woningdossier.cooperation.admin.users.show.tabs.comments-on-building.title')
                </a>
            </li>
            @if(Auth::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                <li>
                    <a data-toggle="tab" id="trigger-fill-in-history-tab" href="#fill-in-history">
                        @lang('woningdossier.cooperation.admin.users.show.tabs.fill-in-history.title')
                    </a>
                </li>
            @endif
        </ul>

        <div class="tab-content">
            {{--messages intern (cooperation to cooperation --}}
            <div id="messages-intern" class="tab-pane fade in active">
                @include('cooperation.admin.layouts.includes.intern-message-box', ['privateMessages' => $privateMessages, 'building' => $building])
            </div>
            @can('access-building', $building->id)
                {{--public messages / between the resident and cooperation--}}
                <div id="messages-public" class="tab-pane fade">
                    @include('cooperation.admin.layouts.includes.resident-message-box', ['publicMessages' => $publicMessages, 'building' => $building])
                </div>
            @endcan
            {{-- comments on the building, read only. --}}
            <div id="comments-on-building" class="tab-pane fade">
                <div class="panel">
                    <div class="panel-body">
                        @forelse($buildingNotes as $buildingNote)
                            <p class="pull-right">{{$buildingNote->created_at->format('Y-m-d H:i')}}</p>
                            <p>{{$buildingNote->note}}</p>
                            <hr>
                        @empty
                        @endforelse

                        <form action="{{route('cooperation.admin.building-notes.store')}}" method="post">
                            {{csrf_field()}}
                            <input type="hidden" name="building[id]" value="{{$building->id}}">
                            <div class="form-group">

                                <label for="building-note">@lang('woningdossier.cooperation.admin.coach.buildings.show.tabs.comments-on-building.note')</label>
                                <textarea id="building-note" name="building[note]"
                                          class="form-control">{{old('building.note')}}</textarea>
                            </div>
                            <button type="submit" class="btn btn-default">
                                @lang('woningdossier.cooperation.admin.coach.buildings.show.tabs.comments-on-building.save')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Fill in history or the log --}}
            @if(Auth::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                <div id="fill-in-history" class="tab-pane fade">
                    <div class="panel">
                        <div class="panel-body">
                            <table id="log-table" class="table-responsive table table-striped table-bordered compact nowrap" style="width: 100%">
                                <thead>
                                <tr>
                                    <th>@lang('woningdossier.cooperation.admin.coach.buildings.show.tabs.fill-in-history.table.columns.happened-on')</th>
                                    <th>@lang('woningdossier.cooperation.admin.coach.buildings.show.tabs.fill-in-history.table.columns.message')</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php /** @var \App\Models\Log $log */ ?>
                                @foreach($logs as $log)
                                    <tr>
                                        <td data-sort="{{strtotime($log->created_at->format('d-m-Y H:i'))}}">{{$log->created_at->format('d-m-Y H:i')}}</td>
                                        <td>{{$log->message}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if($userExists)
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group">
                            <a href="{{route('cooperation.admin.buildings.show', ['id' => $previous])}}"
                               type="button" id="previous" class="btn btn-default">
                                <i class="glyphicon glyphicon-chevron-left"></i>
                                @lang('woningdossier.cooperation.admin.users.show.previous')
                            </a>
                            <a href="{{route('cooperation.admin.buildings.show', ['id' => $next])}}"
                               id="observe-building" class="btn btn-default">
                                @lang('woningdossier.cooperation.admin.users.show.next')
                                <i class="glyphicon glyphicon-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('js')
    <script>


        $(document).ready(function () {

            // get some basic information
            var buildingOwnerId = $('input[name=building\\[id\\]]').val();
            var userId = $('input[name=user\\[id\\]]').val();

            var appointmentDate = $('#appointment-date');

            $('table').DataTable();
            // only initialize the datatable if the tab gets shown, if we wont do this the responsive ness wont work cause its hidden
            $('.nav-tabs a').on('shown.bs.tab', function (event) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
            });

            // so when a user changed the appointment date and does not want to save it, we change it back to the value we got onload.
            var originalAppointmentDate = appointmentDate.find('input').val();

            keepNavTabOpenOnRedirect();
            setUrlHashInHiddenInput();
            scrollChatToMostRecentMessage();

            $('.nav-tabs .active a').trigger('shown.bs.tab');

            var currentDate = new Date();
            currentDate.setDate(currentDate.getDate() - 1);

            appointmentDate.datetimepicker({
                showTodayButton: true,
                allowInputToggle: true,
                locale: 'nl',
                format: 'L',
                showClear: true,
            }).on('dp.hide', function (event) {
                var date = appointmentDate.find('input').val();
                var confirmMessage = '';

                if (date.length > 0) {
                    confirmMessage = "@lang('woningdossier.cooperation.admin.users.show.set-appointment-date')"
                } else {
                    confirmMessage = "@lang('woningdossier.cooperation.admin.users.show.set-empty-appointment-date')"
                }

                if (confirm(confirmMessage)) {
                    $.ajax({
                        method: 'POST',
                        url: '{{route('cooperation.admin.building-coach-status.set-appointment-date')}}',
                        data: {
                            building_id: buildingOwnerId,
                            appointment_date: date
                        },
                    }).done(function () {
                        location.reload();
                    })
                } else {
                    var formattedDate = originalAppointmentDate;
                    // if the user does not want to set / change the appointment date
                    // we set the date back to the one we got onload.
                    appointmentDate.find('input').val(formattedDate);
                }
            });

            // delete the current user
            $('#delete-user').click(function () {
                if (confirm('@lang('woningdossier.cooperation.admin.users.show.delete-user')')) {

                    $.ajax({
                        url: '{{route('cooperation.admin.cooperation.users.destroy')}}',
                        method: 'POST',
                        data: {
                            user_id: userId,
                            _method: 'DELETE'
                        }
                    }).done(function () {
                        window.location.href = '{{route('cooperation.admin.cooperation.users.index')}}'
                    })
                }
            });

            $('#building-coach-status').select2({}).on('select2:selecting', function (event) {
                var statusToSelect = $(event.params.args.data.element);

                if (confirm('@lang('woningdossier.cooperation.admin.users.show.set-status')')) {
                    $.ajax({
                        method: 'POST',
                        url: '{{route('cooperation.admin.building-coach-status.set-status')}}',
                        data: {
                            building_id: buildingOwnerId,
                            status: statusToSelect.val(),
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
                    var option = $('#associated-coaches option[value="' + tag.id + '"]');
                    if (option.attr('locked')) {
                        $(container).addClass('select2-locked-tag');
                        tag.locked = true
                    }

                    return tag.text;
                }
            }).on('select2:unselecting', function (event) {
                var optionToUnselect = $(event.params.args.data.element);

                // check if the option is locked
                if (typeof optionToUnselect.attr('locked') === "undefined") {
                    if (confirm('@lang('woningdossier.cooperation.admin.users.show.revoke-access')')) {
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
                var optionToSelect = $(event.params.args.data.element);

                if (confirm('@lang('woningdossier.cooperation.admin.users.show.add-with-building-access')')) {
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
                    var option = $('#role-select option[value="' + tag.id + '"]');
                    if (option.attr('locked')) {
                        $(container).addClass('select2-locked-tag');
                        tag.locked = true
                    }

                    return tag.text;
                }
            })
                .on('select2:selecting', function (event) {
                    var roleToSelect = $(event.params.args.data.element);

                    if (confirm('@lang('woningdossier.cooperation.admin.users.show.give-role')')) {
                        $.ajax({
                            url: '{{route('cooperation.admin.roles.assign-role')}}',
                            method: 'POST',
                            data: {
                                role_id: roleToSelect.val(),
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
                })
                .on('select2:unselecting', function (event) {
                    var roleToUnselect = $(event.params.args.data.element);

                    if (confirm('@lang('woningdossier.cooperation.admin.users.show.remove-role')')) {
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


        /**
         * Sets the hash / fragment in the url.
         */
        function keepNavTabOpenOnRedirect() {
            // get the current url
            var url = document.location.href;

            // scroll to top off page for less retarded behaviour
            window.scrollTo(0, 0);

            // check if the current url matches a hashtag
            if (url.match('#')) {
                // see if there is a tab and show it.
                $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
            }

            // set the hash in url
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });
        }

        /**
         * Function that sets the url has in a hidden input in all the forms on the page, so we can redirect back with the hash / fragment.
         */
        function setUrlHashInHiddenInput() {
            // set the hash in url
            $('.nav-tabs a').on('shown.bs.tab', function () {
                var forms = $('form');
                forms.each(function (index, form) {
                    var fragmentInput = $(form).find('input[name=fragment]');
                    if (fragmentInput.length > 0) {
                        fragmentInput.val(window.location.hash);
                    } else {
                        $(form).append($('<input>').attr('type', 'hidden').attr('name', 'fragment').val(window.location.hash));
                    }
                });
            });
        }

        function scrollChatToMostRecentMessage() {
            $('.nav-tabs a').on('shown.bs.tab', function () {

                var tabId = $(this).attr('href');
                var tab = $(tabId);
                var chat = tab.find('.panel-chat-body')[0];
                if (typeof chat !== "undefined") {
                    chat.scrollTop = chat.scrollHeight - chat.clientHeight;
                }
            });

        }

    </script>
@endpush