@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.date')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.status')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.appointment-date')</th>
                        </tr>
                        </thead>
                        <tbody>
                     
                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($buildingCoachStatuses as $buildingCoachStatus)
                            <?php
                                $mostRecentForBuildingAndCoachId = \App\Models\BuildingCoachStatus::getMostRecentStatusesForBuildingId($buildingCoachStatus->building_id)->where('coach_id', \App\Helpers\Hoomdossier::user()->id)->first();
                                $building = $buildingCoachStatus->building;
                            ?>
                            @if($building instanceof \App\Models\Building)
                                <?php
                                    $user = $building->user()->withoutGlobalScopes()->first();
                                    $userExists = $user instanceof \App\Models\User;
                                    $appointmentDate = !is_null($mostRecentForBuildingAndCoachId->appointment_date) ? \Carbon\Carbon::parse($mostRecentForBuildingAndCoachId->appointment_date)->format('d-m-Y') : '';
                                ?>
                            <tr>
                                <td data-sort="{{$userExists && $user->created_at instanceof \Carbon\Carbon ? strtotime($user->created_at->format('d-m-Y')) : '-'}}">
                                    {{$userExists && $user->created_at instanceof \Carbon\Carbon ? $user->created_at->format('d-m-Y') : '-'}}
                                </td>
                                <td>{{$userExists ? $user->getFullName() : '-'}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.buildings.show', ['buildingId' => $building->id])}}">
                                        {{$building->street}} {{$building->number}} {{$building->extension}}
                                    </a>
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td>
                                    @if($building->isActive())
                                        {{\App\Models\BuildingCoachStatus::getTranslationForStatus($mostRecentForBuildingAndCoachId->status)}}
                                    @else
                                        {{\App\Models\Building::getTranslationForStatus(\App\Models\Building::STATUS_IS_NOT_ACTIVE)}}
                                    @endif
                                </td>
                                <td data-sort="{{strtotime($appointmentDate)}}">
                                    {{$appointmentDate}}
                                </td>
                            </tr>
                            @endif
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

