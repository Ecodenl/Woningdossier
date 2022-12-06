@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap" style="width: 100%">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.appointment-date')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.status')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.date')</th>
                        </tr>
                        </thead>
                        <tbody>


                        @foreach($buildings as $building)
                            <?php
                                /** @var \App\Models\Building $building */
                                $user = $building->user;

                                $userCreatedAtFormatted = optional($user->created_at)->format('d-m-Y');
                                $userCreatedAtStrotime = strtotime($userCreatedAtFormatted);

                                $appointmentDateFormatted = null;

                                if (!is_null($building->appointment_date)) {
                                    $appointmentDateFormatted = \Carbon\Carbon::create($building->appointment_date)->format('Y-m-d H:i');
                                }

                                $appointmentDateStrotime = strtotime($appointmentDateFormatted);
                            ?>
                            <tr>
                                <td data-sort="{{$appointmentDateStrotime ?? '-'}}">
                                    {{$appointmentDateFormatted ?? '-'}}
                                </td>
                                <td>
                                    {{ $building->status }}
                                </td>
                                <td>{{$user->getFullName()}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.buildings.show', ['buildingId' => $building->id])}}">
                                        {{$building->street}} {{$building->number}} {{$building->extension}}
                                    </a>
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td data-sort="{{$appointmentDateStrotime ?? '-'}}">
                                    {{$userCreatedAtFormatted ?? '-'}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">

                    <div style="display: flex; justify-content: center;">
                        <svg style="height: 2rem" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        <svg style="height: 2rem;" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                        </svg>
                        <svg style="height: 2rem;" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                scrollX: true,
                responsive: false,
            });
        });
    </script>
@endpush

