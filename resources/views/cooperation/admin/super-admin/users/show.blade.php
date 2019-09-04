@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('admin/super-admin.users.show.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @include('cooperation.admin.super-admin.users.search')
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">

                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('admin/super-admin.users.show.table.columns.date')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.name')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.street-house-number')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.zip-code')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.city')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.status')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @if($user->building instanceof \App\Models\Building)
                                <?php
                                    $building = $user->building;
                                    $mostRecentBuildingStatus = $building->getMostRecentBuildingStatus();

                                    $userCreatedAtFormatted = optional($user->created_at)->format('d-m-Y');
                                    $userCreatedAtStrotime = strtotime($userCreatedAtFormatted);
                                ?>
                                <tr>
                                    <td data-sort="{{$userCreatedAtStrotime}}">
                                        {{$userCreatedAtFormatted ?? '-'}}
                                    </td>
                                    <td>{{$user->getFullName()}}</td>
                                    <td>
                                        {{$building->street}} {{$building->number}} {{$building->extension}}
                                    </td>
                                    <td>{{$building->postal_code}}</td>
                                    <td>
                                        {{$building->city}}
                                    </td>
                                    <td>
                                        {{$mostRecentBuildingStatus->status->name}}
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
        $(document).ready(function () {
            $('table').dataTable();
        });
    </script>
@endpush