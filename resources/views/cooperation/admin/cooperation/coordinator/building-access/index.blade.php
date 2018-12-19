@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.header')

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.columns.street')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.columns.owner')</th>
                            <th>Coach</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.columns.status')</th>
                            <th>Verleent toegang</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.columns.appointment')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($filteredResults as $i => $building)
                            <tr>
                                <td>{{ $building->city }}</td>
                                <td>{{ $building->street }}</td>
                                <td>{{ str_limit($building->first_name .' '. $building->last_name, 40)}}</td>

                                <?php
                                    $currentBuildingStatuses = $buildingCoachStatuses->where('building_id', $building->id);
                                    // get the last building status for the current building
                                    $lastBuildingCoachStatus = $currentBuildingStatuses->last();
                                    // unique the current building statuses on coach id, if the count is more then one there are multiple coaches involved to this building thing dinges
                                ?>
                                <td>@if($currentBuildingStatuses->unique('coach_id')->count() > 1)
                                        Meerdere coaches gekoppeld aan gebouw
                                    @elseif($lastBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus)
                                        {{$lastBuildingCoachStatus->coach->first_name .' '. $lastBuildingCoachStatus->coach->last_name}}
                                    @else
                                        Nog geen coach gekoppeld
                                    @endif
                                </td>
                                <td>
                                    {{\App\Models\BuildingCoachStatus::getCurrentStatusName($building->id)}}
                                </td>
                                <td>
                                    {{$building->allow_access ? "Ja" : "Nee"}}
                                </td>

                                <td>@if($lastBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus && !empty($lastBuildingCoachStatus->appointment_date))
                                        {{$lastBuildingCoachStatus->appointment_date}}
                                    @else
                                        @lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.no-appointment')
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

            $('#table').DataTable({
                responsive: true,
                columnDefs: [
                    {responsivePriority: 5, targets: 6},
                    {responsivePriority: 4, targets: 5},
                    {responsivePriority: 6, targets: 3},
                    {responsivePriority: 3, targets: 2},
                    {responsivePriority: 2, targets: 1},
                    {responsivePriority: 1, targets: 0}
                ],
            });

            $('.remove').click(function () {
                if (confirm("Weet u zeker dat u de gebruiker wilt verwijderen")) {

                } else {
                    event.preventDefault();
                }
            })
        })


    </script>
@endpush
