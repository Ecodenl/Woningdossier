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

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="building-coach-status">@lang('woningdossier.cooperation.admin.coach.buildings.edit.form.status')</label>
                        <select class="form-control" name="user[building_coach_status][status]"
                                id="building-coach-status">
                            @foreach(__('woningdossier.building-coach-statuses') as $buildingCoachStatusKey => $buildingCoachStatusName)
                                <option value="{{$buildingCoachStatusKey}}">{{$buildingCoachStatusName}}</option>

                            @endforeach
                            <option value="">@lang('woningdossier.building-coach-statuses.awaiting-status')</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="role-select">@lang('woningdossier.cooperation.admin.cooperation.users.show.role.label')</label>
                        <select class="form-control" name="user[roles]" id="role-select" multiple="multiple">
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="associated-coaches">@lang('woningdossier.cooperation.admin.cooperation.users.show.associated-coach.label')</label>
                        <select name="user[associated_coaches]" id="associated-coaches" class="form-control" multiple="multiple">
                            @foreach($coaches as $coach)
                                <?php $coachBuildingStatus = $coachesWithActiveBuildingCoachStatus->where('coach_id', $coach->id) instanceof stdClass ?>
                                <option @if($coachesWithActiveBuildingCoachStatus->contains('coach_id', $coach->id)) selected="selected" @endif value="{{$coach->id}}">
                                    {{$coach->getFullName()}}
                                    @if($coachBuildingStatus instanceof stdClass && $coachBuildingStatus->count_building_permission > 0)
                                        @lang('woningdossier.cooperation.admin.cooperation.users.show.has-building-access.yes')
                                    @else
                                        @lang('woningdossier.cooperation.admin.cooperation.users.show.has-building-access.no')
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="appointment-date">@lang('woningdossier.cooperation.admin.coach.buildings.edit.form.appointment-date')</label>
                        <div class='input-group date' id="appointment-date">
                            <input id="appointment-date" name="user[building_coach_status][appointment_date]" type='text' class="form-control" value="{{$lastKnownBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus ? $lastKnownBuildingCoachStatus->appointment_date : ''}}"/>
                            <span class="input-group-addon">
                               <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @can('delete-user')
                    <div class="col-sm-6">
                        <p>@lang('woningdossier.cooperation.admin.cooperation.users.show.delete-account.label')</p>
                        <a class="btn btn-danger" id="delete-user">
                            @lang('woningdossier.cooperation.admin.cooperation.users.show.delete-account.button')
                        </a>
                    </div>
                    @endcan
                    <div class="col-sm-6">
                        <p>@lang('woningdossier.cooperation.admin.cooperation.users.show.observe-building.label')</p>
                        <a class="btn btn-primary" id="observe-building">
                            @lang('woningdossier.cooperation.admin.cooperation.users.show.observe-building.button')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // select2-removing
        $(document).ready(function () {
            // pretty selects.
            var associatedCoaches = $('#associated-coaches');
            $('#building-coach-status').select2();
            $('#role-select').select2();
            associatedCoaches.select2();

            associatedCoaches.on('select2:unselecting', function (event) {
                var optionToUnselect = event.params.args.data.element


            });

            $('#appointment-date').datetimepicker({
                format: "YYYY-MM-DD HH:mm",
                locale: '{{app()->getLocale()}}',
            });
        })
    </script>
@endpush