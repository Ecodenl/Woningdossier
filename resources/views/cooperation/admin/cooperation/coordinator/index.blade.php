@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <h4>@lang('woningdossier.cooperation.admin.coach.index.text')</h4>
                    <table class="table table-responsive table-condensed">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.coach.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.index.table.columns.street')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.index.table.columns.owner')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildingPermissions as $buildingPermission)
                            <tr>
                                <td>{{ $buildingPermission->building->city }}</td>
                                <td>{{ $buildingPermission->building->city }}</td>
                                <td>{{ $buildingPermission->building->user->first_name .' '. $buildingPermission->building->user->last_name}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
