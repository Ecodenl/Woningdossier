@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coaches.show.header', [
                'role' => implode(' / ', $roles),
                'full_name' => $userToShow->getFullName(),
                'street' => $buildingFromUser->street,
                'number' => $buildingFromUser->number.' '.$buildingFromUser->extension,
                'zip_code' => strtoupper($buildingFromUser->postal_code),
                'city' => $buildingFromUser->city
            ])
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-responsive table-bordered compact nowrap">
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

                        @foreach($buildings as $building)
                            <?php
                            /**
                             * @var \App\Models\Building $building
                             */
                            $user = $building->user;

                            $userCreatedAtFormatted = $user->created_at?->format('d-m-Y');
                            $userCreatedAtStrotime = strtotime($userCreatedAtFormatted);

                            $appointmentDateFormatted = null;

                            if (!is_null($building->appointment_date)) {
                                $appointmentDateFormatted = \Carbon\Carbon::create($building->appointment_date)->format('Y-m-d H:i');
                            }
                            $appointmentDateStrotime = strtotime($appointmentDateFormatted);

                            $userIsAuthUser = $user->id == Hoomdossier::user()->id;
                            ?>
                            <tr>
                                <td data-sort="{{$userCreatedAtStrotime}}">
                                    {{$userCreatedAtFormatted ?? '-'}}
                                </td>
                                <td>{{$user->getFullName()}}</td>
                                <td>
                                    @if($userIsAuthUser)
                                        <p>{{$building->street}} {{$building->number}} {{$building->extension}}</p>
                                    @else
                                        <a href="{{route('cooperation.admin.buildings.show', ['buildingId' => $building->id])}}">
                                            {{$building->street}} {{$building->number}} {{$building->extension}}
                                        </a>
                                    @endif
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td>
                                    {{ $building->status }}
                                </td>
                                <td data-sort="{{$appointmentDateStrotime}}">
                                    {{$appointmentDateFormatted ?? '-'}}
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
                order: [[0, "desc"]],
                columnDefs: [
                    {responsivePriority: 4, targets: 4},
                    {responsivePriority: 5, targets: 3},
                    {responsivePriority: 3, targets: 2},
                    {responsivePriority: 2, targets: 1},
                    {responsivePriority: 1, targets: 0}
                ],
            });
        });

    </script>
@endpush

