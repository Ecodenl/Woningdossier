@extends('cooperation.admin.layouts.app')

@section('content')
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
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.columns.appointment')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.edit.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildings as $i => $building)
                            <tr>
                                <td>{{ $building->city }}</td>
                                <td>{{ $building->street }}</td>
                                <td>{{ $building->user->getFullName() }}</td>

                                <?php
                                    // get all the statuses for the current building
                                    $buildingStatuses = $building->buildingCoachStatuses;
                                    // get the last known status
                                    $lastBuildingStatus = $buildingStatuses->last();
                                ?>
                                {{-- Check if there are multiple coaches connected to the building --}}
                                <td>@if($buildingStatuses->unique('coach_id')->count() > 1)
                                        @lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.multiple-coaches-connected')
                                    @elseif($lastBuildingStatus instanceof \App\Models\BuildingCoachStatus)
                                        {{$lastBuildingStatus->user->getFullName()}}
                                    @else
                                        @lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.no-coach-connected')
                                    @endif
                                </td>
                                <td>
                                    @if($lastBuildingStatus instanceof \App\Models\BuildingCoachStatus)
                                        @lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.options.'.$lastBuildingStatus->status)
                                    @else
                                        @lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.table.no-status-available')
                                    @endif
                                </td>

                                <td>@if($lastBuildingStatus instanceof \App\Models\BuildingCoachStatus && !empty($lastBuildingStatus->appointment_date))
                                        {{$lastBuildingStatus->appointment_date}}
                                    @else
                                        @lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.index.no-appointment')
                                    @endif
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#private-public-{{$building->id}}" data-building-id="{{$building->id}}" class="participate-in-group-chat btn btn-default">
                                        <i class="glyphicon glyphicon-envelope"></i>
                                    </a>
                                    <a href="{{route('cooperation.admin.cooperation.coordinator.connect-to-coach.create', ['buildingId' => $building->id])}}" class="btn btn-default">
                                        <i class="glyphicon glyphicon-link"></i>
                                    </a>
                                    <a href="{{route('cooperation.admin.cooperation.coordinator.building-access.manage-connected-coaches', ['buildingId' => $building->id])}}" class="btn btn-default">
                                        <i class="glyphicon glyphicon-cog"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('cooperation.layouts.chat.private-public-modal', [
        'buildings' => $buildings,
        'privateRoute' => 'cooperation.admin.cooperation.coordinator.messages.private.edit',
        'publicRoute' => 'cooperation.admin.cooperation.coordinator.messages.public.edit'
    ])

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
