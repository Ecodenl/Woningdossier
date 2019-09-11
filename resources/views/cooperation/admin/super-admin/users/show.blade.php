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

                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive" style="width: 100%">
                        <thead>
                        <tr>
                            <th>@lang('admin/super-admin.users.show.table.columns.cooperation')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.email')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.name')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.street-house-number')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.zip-code')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.city')</th>
                            <th>@lang('admin/super-admin.users.show.table.columns.roles')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @if($user->building instanceof \App\Models\Building)
                                <?php
                                    $building = $user->building;
                                ?>
                                <tr>
                                    <td>
                                        {{$user->cooperation->name}}
                                    </td>
                                    <td>
                                        {{$user->account->email}}
                                    </td>
                                    <td>{{$user->getFullName()}}</td>
                                    <td>
                                        <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show', [
                                            'cooperationToManage' => $user->cooperation,
                                            'user' => $user->id
                                    ])}}">{{$building->street}} {{$building->number}} {{$building->extension}}</a>
                                    </td>
                                    <td>{{$building->postal_code}}</td>
                                    <td>
                                        {{$building->city}}
                                    </td>
                                    <td>
                                        {{implode(', ', $user->roles()->get()->pluck('human_readable_name')->toArray())}}
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