@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/cooperation/residents.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive"
                           style="width: 100%">
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

                                    $userCreatedAtFormatted = optional($user->created_at)->format('d-m-Y');
                                    $userCreatedAtStrToTime = strtotime($userCreatedAtFormatted);
                                @endphp
                                <tr>
                                    <td data-sort="{{$userCreatedAtStrToTime}}">
                                        {{$userCreatedAtFormatted ?? '-'}}
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
                                    <td>
                                        @if($mostRecentBuildingStatus instanceof \App\Models\BuildingStatus)
                                            {{$mostRecentBuildingStatus->status->name}}
                                        @endif
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
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            let table = $('table');
            table.DataTable({
                responsive: true,
                stateSave: true,
                order: [[0, "desc"]],
                columns: [
                    {responsivePriority: 1},
                    {responsivePriority: 2},
                    {responsivePriority: 3},
                    {responsivePriority: 4},
                    {responsivePriority: 6},
                    {responsivePriority: 5},
                    {responsivePriority: 7}
                ]
            });
        })
    </script>
@endpush