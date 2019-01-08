@extends('cooperation.admin.cooperation.cooperation-admin.layouts.app')

@section('cooperation_admin_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.assign-roles.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive table-striped table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.assign-roles.index.table.columns.first-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.assign-roles.index.table.columns.last-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.assign-roles.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.assign-roles.index.table.columns.role')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.assign-roles.index.table.columns.actions')</th>
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
                                <td>
                                    <a href="{{route('cooperation.admin.cooperation.cooperation-admin.assign-roles.edit', ['userId' => $user->id])}}" class="btn btn-primary">Rol toewijzen</a>
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
            $('table').DataTable({
                responsive: true,
                columnDefs: [
                    { responsivePriority: 4, targets: 4 },
                    { responsivePriority: 3, targets: 3 },
                    { responsivePriority: 5, targets: 2 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 1, targets: 0 }
                ]
            });

        })
    </script>
@endpush



