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
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.appointment')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildings as $i => $building)
                            <tr>
                                <td>{{ $building->city }}</td>
                                <td>{{ $building->street }}</td>
                                @if(is_null($building->deleted_at))
                                    <td>{{ str_limit($building->first_name .' '. $building->last_name, 40)}}</td>
                                @else
                                    <td>-</td>
                                @endif
                                <td>
                                    {{\App\Models\BuildingCoachStatus::getCurrentStatus($building->id)}}
                                </td>
                                <td>@if(!$buildingCoachStatuses->isEmpty() && $buildingCoachStatuses->where('building_id', $building->id)->first() instanceof \App\Models\BuildingCoachStatus)
                                        {{$buildingCoachStatuses->where('building_id', $building->id)->first()->appointment_date}}
                                    @else
                                    @endif
                                </td>
                                <td>
                                    @if(is_null($building->deleted_at))
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

