@extends('cooperation.admin.layouts.app')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index.header')

        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index.table.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index.table.email')</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->getFullName()}}</td>
                                <td>{{$user->email}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
