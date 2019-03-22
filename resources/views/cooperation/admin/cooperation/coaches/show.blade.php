@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coaches.show.header', [
                'role' => implode(' / ', $roles),
                'full_name' => $userToShow->getFullName(),
                'street' => $buildingFromUser->street,
                'number' => $buildingFromUser->number,
                'zip_code' => strtoupper($buildingFromUser->postal_code),
                'city' => $buildingFromUser->city
            ])
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.show.table.columns.date')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.show.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.show.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.show.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.show.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.show.table.columns.status')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.show.table.columns.appointment-date')</th>
                        </tr>
                        </thead>
                        <tbody>
                     
                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($buildingCoachStatuses as $buildingCoachStatus)
                            <?php
                                $building = $buildingCoachStatus->building()->withTrashed()->first();
                                $user = $building->user;
                                $userExists = $user instanceof \App\Models\User;
                            ?>

                            <tr>
                                <td data-sort="{{$userExists && $user->created_at instanceof \Carbon\Carbon ? strtotime($user->created_at->format('d-m-Y')) : '-'}}">
                                    {{$userExists && $user->created_at instanceof \Carbon\Carbon ? $user->created_at->format('d-m-Y') : '-'}}
                                </td>
                                <td>{{$userExists ? $user->getFullName() : '-'}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.buildings.show', ['id' => $building->id])}}">
                                        {{$building->street}} {{$building->number}} {{$building->extension}}
                                    </a>
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td>
                                    {{\App\Models\BuildingCoachStatus::getTranslationForStatus($buildingCoachStatus->status)}}
                                </td>
                                <td data-sort="{{$buildingCoachStatus->hasAppointmentDate() ? strtotime($buildingCoachStatus->appointment_date->format('d-m-Y')) : ''}}">
                                    {{$buildingCoachStatus->hasAppointmentDate() ? $buildingCoachStatus->appointment_date->format('d-m-Y') : ''}}
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

