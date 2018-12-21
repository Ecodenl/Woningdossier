@extends('cooperation.admin.cooperation.cooperation-admin.layouts.app')

@section('cooperation_admin_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.index.header')
            <a href="{{route('cooperation.admin.cooperation.cooperation-admin.users.create')}}" class="btn btn-md btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span></a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.index.table.columns.first-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.index.table.columns.last-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.index.table.columns.role')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    <?php
                                        $user->roles->map(function ($role) {
                                            echo ucfirst($role->human_readable_name) .', ';
                                        })
                                    ?>
                                </td>
                            </tr>
                        @empty
                        @endforelse
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
                responsive: true
            });

            $('.remove').click(function () {
                if (confirm("Weet u zeker dat u de gebruiker wilt verwijderen")) {

                } else {
                    event.preventDefault();
                }
            })
        })
    </script>
@endpush

