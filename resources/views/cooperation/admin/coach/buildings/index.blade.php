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
                            <?php $building =  $buildingPermission->building()->withTrashed()->first()?>

                            <tr>
                                <td>{{ $building->city }}</td>
                                <td>{{ $building->city }}</td>
                                @if(!$building->trashed())
                                    <td>{{ $building->user->first_name .' '. $building->user->last_name}}</td>
                                @else
                                    <td>-</td>
                                @endif
                                <td>
                                    @foreach(__('woningdossier.cooperation.admin.coach.buildings.index.table.options') as $buildingCoachStatusKey => $buildingCoachStatusName)
                                        @if(\App\Models\BuildingCoachStatus::currentStatus($buildingCoachStatusKey)->first() instanceof \App\Models\BuildingCoachStatus) {{$buildingCoachStatusName}}@endif
                                    @endforeach
                                </td>
                                <td>
                                    @if(!$building->trashed())
                                    <a href="{{ route('cooperation.admin.coach.buildings.edit', ['id' => $building->id]) }}" class="btn btn-primary"><i class="glyphicon glyphicon-pencil"></i></a>
                                    <a href="{{ route('cooperation.admin.coach.buildings.fill-for-user', ['id' => $building->id]) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    <a href="{{ route('cooperation.admin.coach.buildings.details.index', ['id' => $building->id]) }}" class="btn btn-success"><i class="glyphicon glyphicon-eye-open"></i></a>
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
        $('#appointmentdate').datetimepicker({
            locale: 'nl',

        });

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