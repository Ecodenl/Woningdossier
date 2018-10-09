@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive table-condensed">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.street')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.owner')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.status')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildingPermissions as $buildingPermission)
                            <tr>
                                <td>{{ $buildingPermission->building->city }}</td>
                                <td>{{ $buildingPermission->building->city }}</td>
                                <td>{{ $buildingPermission->building->user->first_name .' '. $buildingPermission->building->user->last_name}}</td>
                                <td>
                                    <div class="input-group" id="current-building-status">
                                        <input disabled placeholder="@lang('woningdossier.cooperation.admin.coach.buildings.index.table.status')" type="text" class="form-control disabled" aria-label="...">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @lang('woningdossier.cooperation.admin.coach.buildings.index.table.status')
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">

                                                @foreach(__('woningdossier.cooperation.admin.coach.buildings.index.table.options') as $buildingCoachStatusKey => $buildingCoachStatus)
                                                    <form action="{{route('cooperation.admin.coach.buildings.set-building-status')}}" method="post">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="building_coach_status" value="{{$buildingCoachStatusKey}}">
                                                        <input type="hidden" name="building_id" value="{{$buildingPermission->building->id}}">
                                                        <li><a href="javascript:;" @if(\App\Models\BuildingCoachStatus::currentStatus($buildingCoachStatusKey)->first() instanceof \App\Models\BuildingCoachStatus) id="current" @endif onclick="parentNode.parentNode.submit()" href="">{{$buildingCoachStatus}}</a></li>
                                                    </form>
                                                @endforeach
                                            </ul>
                                        </div><!-- /btn-group -->
                                    </div><!-- /input-group -->
                                </td>
                                <td>
                                    <a href="{{ route('cooperation.admin.coach.buildings.fill-for-user', ['id' => $buildingPermission->building->id]) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                                    <a href="{{ route('cooperation.admin.coach.buildings.details.index', ['id' => $buildingPermission->building->id]) }}" class="btn btn-success"><i class="glyphicon glyphicon-eye-open"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function () {
            // put the label text from the selected option inside the input for ux
            var buildingCoachStatus = $('#current-building-status');
            var input = $(buildingCoachStatus).find('input.form-control');
            var currentStatus = $(buildingCoachStatus).find('li a[id=current]');

            var inputValPrefix = '{{__('woningdossier.cooperation.admin.coach.buildings.index.table.current-status')}} ';

            $(input).val(inputValPrefix + $(currentStatus).text().trim());
        });
    </script>
@endpush