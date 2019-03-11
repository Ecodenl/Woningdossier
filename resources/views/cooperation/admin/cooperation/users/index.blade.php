@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.users.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.date')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.status')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($users as $user)
                            <?php $building = $user->buildings()->first(); ?>
                            <tr>
                                <td>{{$user->created_at instanceof \Carbon\Carbon ? $user->created_at->format('d-m-Y') : __('woningdossier.cooperation.admin.cooperation.users.index.table.columns.no-known-created-at')}}</td>
                                <td>{{$user->getFullName()}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.cooperation.users.show', ['id' => $user->id])}}">
                                        {{$building->street}} {{$building->house_number}} {{$building->house_number_ext}}
                                    </a>
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td>
                                    <?php $lastKnownBuildingCoachStatus = $building->buildingCoachStatuses->last() ?>
                                    @if($lastKnownBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus && !empty($lastKnownBuildingCoachStatus->appointment_date))
                                        {{$lastKnownBuildingCoachStatus->appointment_date}}
                                    @else
                                        @lang('woningdossier.cooperation.admin.coach.buildings.index.no-appointment')
                                    @endif
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
            var table = $('table');
            table.DataTable({
                responsive: true,
                columns: [
                    { responsivePriority: 1 },
                    { responsivePriority: 2 },
                    { responsivePriority: 3 },
                    { responsivePriority: 4 },
                    { responsivePriority: 6 },
                    { responsivePriority: 5 }
                ]
            });
        })
    </script>
@endpush