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
                                    <a href="{{ route('cooperation.admin.coach.buildings.fill-for-user', ['id' => $buildingPermission->building->id]) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                                    <form style="display:inline;" action="{{ route('cooperation.admin.cooperation.cooperation-admin.example-buildings.destroy', ['id' => $buildingPermission->building->id]) }}" method="post">
                                        {{ method_field("DELETE") }}
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
                                    </form>
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
