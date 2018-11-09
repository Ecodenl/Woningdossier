@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.street')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.owner')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.status')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.appointment')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildingsFromCoachStatuses as $i => $building)
                            <tr>
                                <td>{{ $building->city }}</td>
                                <td>{{ $building->street }}</td>
                                @if(is_null($building->deleted_at))
                                    <td>{{ str_limit($building->first_name .' '. $building->last_name, 40)}}</td>
                                @else
                                    <td>-</td>
                                @endif
                                <td>
                                    {{\App\Models\BuildingCoachStatus::getCurrentStatusName($building->id)}}
                                </td>
                                <td>@if($buildingCoachStatuses->where('building_id', $building->id)->last() instanceof \App\Models\BuildingCoachStatus && !empty($buildingCoachStatuses->where('building_id', $building->id)->last()->appointment_date))
                                        {{$buildingCoachStatuses->where('building_id', $building->id)->last()->appointment_date}}
                                    @else
                                        @lang('woningdossier.cooperation.admin.coach.buildings.index.no-appointment')
                                    @endif
                                </td>
                                <td>
                                    @if(empty($building->deleted_at))
                                        <a href="{{ route('cooperation.admin.coach.buildings.edit', ['id' => $building->id]) }}" class="btn btn-primary"><i class="glyphicon glyphicon-pencil"></i></a>
                                    {{--TODO: do not only do this on building level, should be double checked in building permissions table. --}}
                                        @if($building->allow_access)
                                            <a href="{{ route('cooperation.admin.coach.buildings.fill-for-user', ['id' => $building->id]) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                                        @endif
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

        $('#table').DataTable({
            responsive: true,
            columnDefs: [
                {responsivePriority: 4, targets: 4},
                {responsivePriority: 5, targets: 3},
                {responsivePriority: 3, targets: 2},
                {responsivePriority: 2, targets: 1},
                {responsivePriority: 1, targets: 0}
            ],
        });

    </script>
@endpush

