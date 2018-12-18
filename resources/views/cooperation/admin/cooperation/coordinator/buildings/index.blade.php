@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.header')

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.buildings.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.buildings.index.table.columns.street')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.buildings.index.table.columns.owner')</th>
                            <th>Coach toegang</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.buildings.index.table.columns.status')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.buildings.index.table.columns.appointment')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.buildings.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($residentsThatGaveAccessToBuilding as $i => $building)
                            <tr>
                                <td>{{ $building->city }}</td>
                                <td>{{ $building->street }}</td>
                                <td>{{ str_limit($building->first_name .' '. $building->last_name, 40)}}</td>

                                <?php
                                    // check if there are more then 1 building statuses to
                                    $lastBuildingCoachStatus = $buildingCoachStatuses->where('building_id', $building->id)->last()
                                ?>
                                <td>@if($buildingCoachStatuses->where('building_id', $building->id)->count() > 1)
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

                                <td>@if($lastBuildingCoachStatus instanceof \App\Models\BuildingCoachStatus && !empty($lastBuildingCoachStatus->appointment_date))
                                        {{$lastBuildingCoachStatus->appointment_date}}
                                    @else
                                        @lang('woningdossier.cooperation.admin.cooperation.coordinator.buildings.index.no-appointment')
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cooperation.admin.cooperation.coordinator.connect-to-coach.create', ['privateMessageId' => $building->private_message_id]) }}" class="btn btn-success"><i class="glyphicon glyphicon-link"></i> Koppel met coach</a>
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
                    {responsivePriority: 4, targets: 4},
                    {responsivePriority: 5, targets: 3},
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
