@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.coach.buildings.index.header')
])

@section('content')
    <div class="w-full">
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
                <th>@lang('cooperation/admin/cooperation/residents.index.table.columns.email-verified')</th>
            </tr>
            </thead>
            <tbody>


            @foreach($buildings as $building)
                @php
                    /** @var \App\Models\Building $building */
                    $user = $building->user;

                    $userCreatedAtFormatted = $user->created_at?->format('d-m-Y');
                    $userCreatedAtStrotime = strtotime($userCreatedAtFormatted);

                    $appointmentDateFormatted = null;

                    if (!is_null($building->appointment_date)) {
                        $appointmentDateFormatted = \Carbon\Carbon::create($building->appointment_date)->format('Y-m-d H:i');
                    }

                    $appointmentDateStrotime = strtotime($appointmentDateFormatted);
                @endphp
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
                    <td data-sort="{{$userCreatedAtStrotime ?? '-'}}">
                        {{$userCreatedAtFormatted ?? '-'}}
                    </td>
                    <td>
                        @can('verifyEmail', $user->account)
                            <a class="btn btn-success"
                               href="{{ route('cooperation.admin.actions.verify-email', ['account' => $user->account]) }}">
                                @lang('default.verify')
                            </a>
                        @else
                            {{ (empty($user->account) || is_null($user->account->email_verified_at)) ? __('default.no') : $user->account->email_verified_at->format('d-m-Y') }}
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            $('#table').DataTable({
                scrollX: true,
                responsive: false,
            });
        });
    </script>
@endpush

