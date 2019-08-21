@extends('cooperation.admin.layouts.app')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.header')

        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.created-at')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.actions')</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($users as $user)
                            <tr>
                                <td data-sort="{{$user->created_at instanceof \Carbon\Carbon ? strtotime($user->created_at->format('d-m-Y')) : '-'}}">
                                    {{$user->created_at instanceof \Carbon\Carbon ? $user->created_at->format('d-m-Y') : '-'}}
                                </td>
                                <td>{{$user->getFullName()}}</td>
                                <td>{{$user->account->email}}</td>
                                <td>
                                    <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show', [
                                        'cooperationToManage' => $cooperationToManage,
                                        'user' => $user->id
                                    ])}}">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </a>
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
            $('table').dataTable();
        });
    </script>
@endpush