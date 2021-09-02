@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.users.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive"
                           style="width: 100%">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.date')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.status')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        /**
                         * @var \App\Models\User $user
                         * @var \App\Models\Building $building
                         */
                        ?>
                        @foreach($users as $user)
                            <?php
                                $building = $user->building;
                                $mostRecentBuildingStatus = $building->buildingStatuses->last();

                                $userCreatedAtFormatted = optional($user->created_at)->format('d-m-Y');
                                $userCreatedAtStrotime = strtotime($userCreatedAtFormatted);
                            ?>
                            <tr>
                                <td data-sort="{{$userCreatedAtStrotime}}">
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

            var table = $('table');
            table.DataTable({
                responsive: true,
                order: [[0, "desc"]],
                columns: [
                    {responsivePriority: 1},
                    {responsivePriority: 2},
                    {responsivePriority: 3},
                    {responsivePriority: 4},
                    {responsivePriority: 6},
                    {responsivePriority: 5}
                ]
            });
        })
    </script>
@endpush