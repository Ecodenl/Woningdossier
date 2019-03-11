@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.users.show.header', [
                'name' => $user->getFullName(),
                'street-and-number' => $building->street.' '.$building->house_number.$building->house_number_extension,
                'zipcode-and-city' => $building->postal_code.' '.$building->city
            ])
        </div>

        <input type="hidden" name="building[id]" value="{{$building->id}}">
        <input type="hidden" name="user[id]" value="{{$user->id}}">
        <div class="panel-body">
            {{--delete a pipo--}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="btn-group">
                        @can('delete-user')
                            <button type="button" id="delete-user" class="btn btn-danger">
                                @lang('woningdossier.cooperation.admin.cooperation.users.show.delete-account.label')
                                @lang('woningdossier.cooperation.admin.cooperation.users.show.delete-account.button')
                            </button>
                        @endcan
                        <button type="button" id="observe-building" class="btn btn-primary">
                            @lang('woningdossier.cooperation.admin.cooperation.users.show.observe-building.label')
                            @lang('woningdossier.cooperation.admin.cooperation.users.show.observe-building.button')
                        </button>
                    </div>
                </div>
            </div>
            {{--status and roles--}}
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="building-coach-status">@lang('woningdossier.cooperation.admin.coach.buildings.edit.form.status')</label>
                        <select disabled class="form-control" name="user[building_coach_status][status]"
                                id="building-coach-status">
                            @foreach(__('woningdossier.building-coach-statuses') as $buildingCoachStatusKey => $buildingCoachStatusName)
                                <option value="{{$buildingCoachStatusKey}}">{{$buildingCoachStatusName}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="role-select">@lang('woningdossier.cooperation.admin.cooperation.users.show.role.label')</label>
                        <select class="form-control" name="user[roles]" id="role-select" multiple="multiple">
                            @foreach($roles as $role)
                                <option @if($user->hasRole($role)) selected="selected"
                                        @endif value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            {{--coaches and appointmentdate--}}
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="associated-coaches">@lang('woningdossier.cooperation.admin.cooperation.users.show.associated-coach.label')</label>
                        <select name="user[associated_coaches]" id="associated-coaches" class="form-control"
                                multiple="multiple">
                            @foreach($coaches as $coach)
                                <?php $coachBuildingStatus = $coachesWithActiveBuildingCoachStatus->where('coach_id', $coach->id) instanceof stdClass ?>
                                <option
                                        @if($coach->hasRole(['cooperation-admin', 'coordinator'])) locked="locked"
                                        @endif
                                        @if($coachesWithActiveBuildingCoachStatus->contains('coach_id', $coach->id)) selected="selected"
                                        @endif value="{{$coach->id}}">
                                    {{$coach->getFullName()}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="appointment-date">@lang('woningdossier.cooperation.admin.coach.buildings.edit.form.appointment-date')</label>
                        <div class='input-group date' id="appointment-date">
                            <input disabled id="appointment-date" name="user[building_coach_status][appointment_date]"
                                   type='text' class="form-control"
                                   value="{{$lastKnownBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus ? $lastKnownBuildingCoachStatus->appointment_date : ''}}"/>
                            <span class="input-group-addon">
                               <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs">

            <li class="active">
                <a data-toggle="tab" href="#messages-intern">
                    @lang('woningdossier.cooperation.admin.cooperation.users.show.tabs.messages-intern.title')
                </a>
            </li>
            @if($user->allowedAccessToHisBuilding($building->id))
            <li>
                <a data-toggle="tab" href="#messages-public">
                    @lang('woningdossier.cooperation.admin.cooperation.users.show.tabs.messages-public.title')
                </a>
            </li>
            @endif

            <li>
                <a data-toggle="tab" href="#comments-on-building">
                    @lang('woningdossier.cooperation.admin.cooperation.users.show.tabs.comments-on-building.title')
                </a>
            </li>
            @if(Auth::user()->hasRoleAndIsCurrentRole(['cooperation-admin']) && $user->allowedAccessToHisBuilding($building->id))
                <li>
                    <a data-toggle="tab" href="#fill-in-history">
                        @lang('woningdossier.cooperation.admin.cooperation.users.show.tabs.fill-in-history.title')
                    </a>
                </li>
            @endif
        </ul>

        <div class="tab-content">
            <div id="messages-intern" class="tab-pane fade in active">
                <div class="panel">
                    <div class="panel-body panel-chat-body">
                        @component('cooperation.layouts.chat.messages')
                            @forelse($privateMessages as $privateMessage)

                                <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            @if($privateMessage->isMyMessage())

                                                <small class="text-muted">
                                                    <span class="glyphicon glyphicon-time"></span>{{$privateMessage->created_at->diffForHumans()}}
                                                </small>
                                                <strong class="pull-right primary-font">{{$privateMessage->getSender()}}</strong>

                                            @else

                                                <strong class="primary-font">{{$privateMessage->getSender()}}</strong>
                                                <small class="pull-right text-muted">
                                                    <span class="glyphicon glyphicon-time"></span>{{$privateMessage->created_at->diffForHumans()}}
                                                </small>

                                            @endif
                                        </div>
                                        <p>
                                            {{$privateMessage->message}}
                                        </p>
                                    </div>
                                </li>
                            @empty

                            @endforelse
                        @endcomponent
                    </div>
                </div>
                <div class="panel-footer">
                    @component('cooperation.layouts.chat.input', ['privateMessages' => $privateMessages, 'buildingId' => $building->id, 'url' => route('cooperation.admin.cooperation.cooperation-admin.messages.store'), 'isPublic' => false])
                        <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                            @lang('woningdossier.cooperation.admin.coach.messages.edit.send')
                        </button>
                    @endcomponent
                </div>
            </div>
            <div id="messages-public" class="tab-pane fade">
                <div class="panel">
                    <div class="panel-body panel-chat-body">
                        @component('cooperation.layouts.chat.messages')
                            @forelse($publicMessages as $publicMessage)

                                <li class="@if($publicMessage->isMyMessage()) right @else left @endif clearfix">
                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            @if($publicMessage->isMyMessage())

                                                <small class="text-muted">
                                                    <span class="glyphicon glyphicon-time"></span>{{$publicMessage->created_at->diffForHumans()}}
                                                </small>
                                                <strong class="pull-right primary-font">{{$publicMessage->getSender()}}</strong>

                                            @else

                                                <strong class="primary-font">{{$publicMessage->getSender()}}</strong>
                                                <small class="pull-right text-muted">
                                                    <span class="glyphicon glyphicon-time"></span>{{$publicMessage->created_at->diffForHumans()}}
                                                </small>

                                            @endif
                                        </div>
                                        <p>
                                            {{$publicMessage->message}}
                                        </p>
                                    </div>
                                </li>
                            @empty

                            @endforelse
                        @endcomponent
                    </div>
                    <div class="panel-footer">
                        @component('cooperation.layouts.chat.input', ['privateMessages' => $privateMessages, 'buildingId' => $building->id, 'url' => route('cooperation.admin.cooperation.cooperation-admin.messages.store'), 'isPublic' => true])
                            <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                                @lang('woningdossier.cooperation.admin.coach.messages.edit.send')
                            </button>
                        @endcomponent
                    </div>
                </div>
            </div>
            <div id="comments-on-building" class="tab-pane fade">
                <div class="panel">
                    <div class="panel-body">

                        @forelse($buildingNotes as $buildingNote)
                            <p class="pull-right">{{$buildingNote->created_at->format('Y-m-d H:i')}}</p>
                            <p>{{$buildingNote->note}}</p>
                            <hr>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
            <div id="fill-in-history" class="tab-pane fade">
                <div class="panel">
                    <div class="panel-body">
                        content.
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12">
                    <div class="btn-group">
                        <a href="{{route('cooperation.admin.cooperation.users.show', ['id' => $previous])}}" type="button" id="previous" class="btn btn-default">
                            <i class="glyphicon glyphicon-chevron-left"></i>
                            @lang('woningdossier.cooperation.admin.cooperation.users.show.previous')
                        </a>
                        <a href="{{route('cooperation.admin.cooperation.users.show', ['id' => $next])}}" id="observe-building" class="btn btn-default">
                            @lang('woningdossier.cooperation.admin.cooperation.users.show.next')
                            <i class="glyphicon glyphicon-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            // get some basic information
            var buildingOwnerId = $('input[name=building\\[id\\]]').val();
            var userId = $('input[name=user\\[id\\]]').val();

            // scrollChatsToBottom();
            keepNavTabOpenOnRedirect();
            setUrlHashInHiddenInput();
            scrollChatToMostRecentMessage();
            $('.nav-tabs .active a').trigger('shown.bs.tab');

            $('#appointment-date').datetimepicker({
                format: "YYYY-MM-DD HH:mm",
                locale: '{{app()->getLocale()}}',
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
                    if (confirm('@lang('woningdossier.cooperation.admin.cooperation.users.show.revoke-access')')) {
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

                if (confirm('@lang('woningdossier.cooperation.admin.cooperation.users.show.add-with-building-access')')) {
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

            $('#role-select').select2()
                .on('select2:selecting', function (event) {
                    var roleToSelect = $(event.params.args.data.element);

                    if (confirm('@lang('woningdossier.cooperation.admin.cooperation.users.show.give-role')')) {
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

                    if (confirm('@lang('woningdossier.cooperation.admin.cooperation.users.show.remove-role')')) {
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

                console.log('bier');
                chat.scrollTop = chat.scrollHeight - chat.clientHeight;
            });

        }

    </script>
@endpush