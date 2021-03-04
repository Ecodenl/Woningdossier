@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/buildings.show.header', [
                'name' => $user->getFullName(),
                'street-and-number' => $building->street.' '.$building->number.' '.$building->extension,
                'zipcode-and-city' => $building->postal_code.' '.$building->city,
                'email' => $user->account->email,
                'phone-number' => $user->phone_number
            ])
        </div>

        <input type="hidden" name="building[id]" value="{{$building->id}}">
        <input type="hidden" id="cooperation-id" value="{{\App\Helpers\HoomdossierSession::getCooperation()}}">

        <input type="hidden" name="user[id]" value="{{$user->id}}">
        <div class="panel-body">
            {{-- delete a user --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="btn-group">
                        @can('delete-user', $user)
                            <button type="button" id="delete-user" class="btn btn-danger">
                                @lang('cooperation/admin/buildings.show.delete-account.label')
                                @lang('cooperation/admin/buildings.show.delete-account.button')
                            </button>
                        @endcan
                        @can('access-building', $building)
                            <a href="{{route('cooperation.admin.tool.observe-tool-for-user', compact('building'))}}" id="observe-building" class="btn btn-primary">
                                @lang('cooperation/admin/buildings.show.observe-building.label')
                                @lang('cooperation/admin/buildings.show.observe-building.button')
                            </a>
                            @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('coach'))
                                <a href="{{route('cooperation.admin.tool.fill-for-user', compact('building'))}}"
                                   id="edit-building" class="btn btn-warning">
                                    @lang('cooperation/admin/buildings.show.fill-for-user.label')
                                    @lang('cooperation/admin/buildings.show.fill-for-user.button')
                                </a>
                            @endif
                        @endcan
                        @can('edit', $building)
                            <a href="{{route('cooperation.admin.buildings.edit', compact('building'))}}" id="edit-building" class="btn btn-success">
                                @lang('cooperation/admin/buildings.show.edit.label')
                                @lang('cooperation/admin/buildings.show.edit.button')
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            {{-- status and appointment date --}}
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="building-coach-status">@lang('cooperation/admin/buildings.show.status.label')</label>
                        <select autocomplete="off" class="form-control" name="building[building_statuses][id]" id="building-status">
                            @foreach($statuses as $status)
                                <option {{$mostRecentStatus->status_id == $status->id ? 'selected="selected"' : ''}} value="{{$status->id}}">
                                    @if($mostRecentStatus->status_id == $status->id)
                                        @lang('cooperation/admin/buildings.show.status.current')
                                    @endif
                                    {{$status->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="appointment-date">@lang('cooperation/admin/buildings.show.appointment-date.label')</label>
                        <div class='input-group date' id="appointment-date">
                            <input autocomplete="off" id="appointment-date" name="building[building_statuses][appointment_date]" type='text' class="form-control"
                                   @if($mostRecentStatus instanceof \App\Models\BuildingStatus && $mostRecentStatus->hasAppointmentDate())
                                       value=" {{$mostRecentStatus->appointment_date->format('d-m-Y')}}"
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
                @if($publicMessages->isNotEmpty())
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="associated-coaches">@lang('cooperation/admin/buildings.show.associated-coach.label')</label>
                            <select @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) disabled @endif name="user[associated_coaches]" id="associated-coaches" class="form-control" multiple="multiple">
                                @foreach($coaches as $coach)
                                    <?php $coachBuildingStatus = $coachesWithActiveBuildingCoachStatus->where('coach_id', $coach->id) instanceof stdClass ?>
                                    <option
                                            @if($coachesWithActiveBuildingCoachStatus->contains('coach_id', $coach->id))
                                                selected="selected"
                                            @endif
                                            value="{{$coach->id}}">{{$coach->getFullName()}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="role-select">@lang('cooperation/admin/buildings.show.role.label')</label>
                        <select @if($building->id == \App\Helpers\HoomdossierSession::getBuilding() || Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) disabled @endif
                                class="form-control" name="user[roles]" id="role-select" multiple="multiple">
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
            </div>
        </div>

        <ul class="nav nav-tabs">

            <li @if(session('fragment') == 'messages-intern') class="active" @endif>
                <a data-toggle="tab" href="#messages-intern">
                    @lang('cooperation/admin/buildings.show.tabs.messages-intern.title')
                </a>
            </li>

            @can('talk-to-resident', [$building])
                <li @if(session('fragment') == 'messages-public' || empty(session('fragment'))) class="active" @endif>
                    <a data-toggle="tab" href="#messages-public">
                        @if($user->retrievesNotifications(\App\Models\NotificationType::PRIVATE_MESSAGE))
                            <i class="glyphicon glyphicon-bell" data-placement="top" data-toggle="tooltip" title="@lang('cooperation/admin/buildings.show.tabs.messages-public.user-notification.yes')"></i>
                        @else
                            <i class="glyphicon glyphicon-ban-circle" data-placement="top" data-toggle="tooltip" title="@lang('cooperation/admin/buildings.show.tabs.messages-public.user-notification.no')"></i>
                        @endif
                        @lang('cooperation/admin/buildings.show.tabs.messages-public.title')
                    </a>
                </li>
            @endcan
            <li @if(session('fragment') == 'comments-on-building') class="active" @endif>
                <a data-toggle="tab" href="#comments-on-building">
                    @lang('cooperation/admin/buildings.show.tabs.comments-on-building.title')
                </a>
            </li>
            @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                <li>
                    <a data-toggle="tab" id="trigger-fill-in-history-tab" href="#fill-in-history">
                        @lang('cooperation/admin/buildings.show.tabs.fill-in-history.title')
                    </a>
                </li>
            @endif
        </ul>

        <div class="tab-content">
            {{--messages intern (cooperation to cooperation --}}
            <div id="messages-intern" class="tab-pane fade @if(session('fragment') == 'messages-intern' ) in active @endif">
                @include('cooperation.admin.buildings.parts.message-box', ['messages' => $privateMessages, 'building' => $building, 'isPublic' => false])
            </div>
            @can('talk-to-resident', [$building])
                {{--public messages / between the resident and cooperation--}}
                <div id="messages-public" class="tab-pane fade @if(session('fragment') == 'messages-public' || empty(session('fragment'))) in active @endif">
                    @include('cooperation.admin.buildings.parts.message-box', ['messages' => $publicMessages, 'building' => $building, 'isPublic' => true])
                </div>
            @endcan


            {{-- comments on the building, read only. --}}
            <div id="comments-on-building" class="tab-pane fade @if(session('fragment') == 'comments-on-building' ) in active @endif">
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

                                <label for="building-note">@lang('cooperation/admin/buildings.show.tabs.comments-on-building.note')</label>
                                <textarea id="building-note" name="building[note]" class="form-control">{{old('building.note')}}</textarea>
                            </div>
                            <button type="submit" class="btn btn-default">
                                @lang('cooperation/admin/buildings.show.tabs.comments-on-building.save')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Fill in history ?? the log --}}
            @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                <div id="fill-in-history" class="tab-pane fade">
                    <div class="panel">
                        <div class="panel-body">
                            <table id="log-table"
                                   class="table-responsive table table-striped table-bordered compact nowrap"
                                   style="width: 100%">
                                <thead>
                                <tr>
                                    <th>@lang('cooperation/admin/buildings.show.tabs.fill-in-history.table.columns.happened-on')</th>
                                    <th>@lang('cooperation/admin/buildings.show.tabs.fill-in-history.table.columns.message')</th>
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
    </div>
@endsection

@push('js')
    <script>
        // so when a user changed the appointment date and does not want to save it, we change it back to the value we got onload.
        var originalAppointmentDate = @if($mostRecentStatus instanceof \App\Models\BuildingStatus && $mostRecentStatus->hasAppointmentDate()) '{{$mostRecentStatus->appointment_date->format('d-m-Y')}}' @else '' @endif;

        $(document).ready(function () {

            // get some basic information
            var buildingOwnerId = $('input[name=building\\[id\\]]').val();
            var userId = $('input[name=user\\[id\\]]').val();
            var cooperationId = $('#cooperation-id').val();

            var appointmentDate = $('#appointment-date');

            $('table').DataTable({
                'order': [[0, 'desc']]
            });

            // only initialize the datatable if the tab gets shown, if we wont do this the responsive ness wont work cause its hidden
            $('.nav-tabs a').on('shown.bs.tab', function (event) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
            });

            $('[data-toggle="tooltip"]').tooltip();

            scrollChatToMostRecentMessage();
            onFormSubmitAddFragmentToRequest();


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
                appointmentDate.find('input').blur();

                var date = appointmentDate.find('input').val();

                if (date !== originalAppointmentDate) {
                    var confirmMessage = "@lang('cooperation/admin/buildings.show.set-empty-appointment-date')";

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

            // delete the current user
            $('#delete-user').click(function () {
                if (confirm('@lang('cooperation/admin/buildings.show.delete-user')')) {

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

            $('#building-status').select2({}).on('select2:selecting', function (event) {
                var statusToSelect = $(event.params.args.data.element);

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
                var optionToSelect = $(event.params.args.data.element);

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
                    var roleToUnselect = $(event.params.args.data.element);

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

                var tabId = $(this).attr('href');
                var tab = $(tabId);
                var chat = tab.find('.panel-chat-body')[0];

                if (typeof chat !== "undefined") {
                    chat.scrollTop = chat.scrollHeight - chat.clientHeight;

                    var isChatPublic = tab.find('[name=is_public]').val();
                    var buildingId = tab.find('[name=building_id]').val();

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