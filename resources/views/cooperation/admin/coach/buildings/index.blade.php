@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.header')</div>

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
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.status')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.appointment-date')</th>
                        </tr>
                        </thead>
                        <tbody>
                     
                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($buildingsFromCoachStatuses as $user)
                            <?php $building = \App\Models\Building::where('user_id', $user->id)->first() ?>

                            <tr>
                                <td>{{$user->first_name.' '.$user->last_name}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.coach.buildings.show', ['id' => $building->id])}}">
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

    @include('coach.layouts.chat.private-public-modal', [
        'buildings' => $buildingsFromCoachStatuses,
        'privateRoute' => 'cooperation.admin.coach.messages.private.edit',
        'publicRoute' => 'cooperation.admin.coach.messages.public.edit'
    ])

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

