@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/cooperation/residents.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/cooperation/residents.index.table.columns.date')</th>
                    <th>@lang('cooperation/admin/cooperation/residents.index.table.columns.name')</th>
                    <th>@lang('cooperation/admin/cooperation/residents.index.table.columns.street-house-number')</th>
                    <th>@lang('cooperation/admin/cooperation/residents.index.table.columns.zip-code')</th>
                    <th>@lang('cooperation/admin/cooperation/residents.index.table.columns.city')</th>
                    <th>@lang('cooperation/admin/cooperation/residents.index.table.columns.status')</th>
                    <th>@lang('cooperation/admin/cooperation/residents.index.table.columns.email-verified')</th>
                </tr>
            </thead>
            <tbody>
                @php
                    /**
                     * @var \App\Models\User $user
                     * @var \App\Models\Building $building
                     */
                @endphp
                @foreach($users as $user)
                    @php
                        $building = $user->building;
                        $mostRecentBuildingStatus = $building->buildingStatuses->last();

                        $userCreatedAtFormatted = $user->created_at?->format('d-m-Y');
                        $userCreatedAtStrToTime = strtotime($userCreatedAtFormatted);
                    @endphp
                    <tr>
                        <td data-sort="{{$userCreatedAtStrToTime}}">
                            {{$userCreatedAtFormatted ?? '-'}}
                        </td>
                        <td>{{$user->getFullName()}}</td>
                        <td>
                            <a class="in-text" href="{{route('cooperation.admin.buildings.show', compact('building'))}}">
                                {{$building->street}} {{$building->number}} {{$building->extension}}
                            </a>
                        </td>
                        <td>{{$building->postal_code}}</td>
                        <td>
                            {{$building->city}}
                        </td>
                        <td>
                            @if($mostRecentBuildingStatus instanceof \App\Models\BuildingStatus)
                                {{$mostRecentBuildingStatus->status->name}}
                            @endif
                        </td>
                        @can('verifyEmail', $user->account)
                            <td data-order="0">
                                <a class="btn btn-green btn-sm"
                                   href="{{ route('cooperation.admin.actions.verify-email', ['account' => $user->account]) }}">
                                    @lang('default.verify')
                                </a>
                            </td>
                        @else
                            @php
                                $isNotVerified = (empty($user->account) || is_null($user->account->email_verified_at));
                            @endphp
                            <td data-order="{{ $isNotVerified ? 0 : $user->account->email_verified_at->unix() }}">
                                {{ $isNotVerified ? __('default.no') : $user->account->email_verified_at->format('d-m-Y') }}
                            </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
                stateSave: true,
                order: [[0, "desc"]],
                // columnDefs: [
                //     {responsivePriority: 4, targets: 4},
                //     {responsivePriority: 5, targets: 3},
                //     {responsivePriority: 3, targets: 2},
                //     {responsivePriority: 2, targets: 1},
                //     {responsivePriority: 1, targets: 0}
                // ],
                language: {
                    url: '{{ asset('js/datatables-dutch.json') }}'
                },
                layout: {
                    bottomEnd: {
                        paging: {
                            firstLast: false
                        }
                    }
                },
            });
        })
    </script>
@endpush