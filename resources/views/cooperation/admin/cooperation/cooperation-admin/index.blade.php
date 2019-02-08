@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-responsive table-striped table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.index.table.columns.first-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.index.table.columns.last-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.index.table.columns.role')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    <?php
                                    $user->roles->map(function ($role) {
                                        echo ucfirst($role->human_readable_name).', ';
                                    });
                                    ?>
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
            $('#table').DataTable();
        })
    </script>
@endpush