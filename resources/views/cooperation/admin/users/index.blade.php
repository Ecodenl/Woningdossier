@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/users.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/users.index.table.columns.date')</th>
                    <th>@lang('cooperation/admin/users.index.table.columns.name')</th>
                    <th>@lang('cooperation/admin/users.index.table.columns.street-house-number')</th>
                    <th>@lang('cooperation/admin/users.index.table.columns.zip-code')</th>
                    <th>@lang('cooperation/admin/users.index.table.columns.city')</th>
                    <th>@lang('cooperation/admin/users.index.table.columns.status')</th>
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
                            @can('show', $building)
                                <a class="in-text"
                                   href="{{route('cooperation.admin.buildings.show', compact('building'))}}">
                                    {{$building->street}} {{$building->number}} {{$building->extension}}
                                </a>
                            @else
                                {{$building->street}} {{$building->number}} {{$building->extension}}
                            @endcan
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
                scrollX: true,
                stateSave: true,
                order: [[0, "desc"]],
                // responsive: true,
                // columns: [
                //     {responsivePriority: 1},
                //     {responsivePriority: 2},
                //     {responsivePriority: 3},
                //     {responsivePriority: 4},
                //     {responsivePriority: 6},
                //     {responsivePriority: 5}
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