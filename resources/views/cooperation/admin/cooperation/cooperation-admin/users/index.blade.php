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
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.index.table.columns.actions')</th>
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
                                            echo ucfirst($role->human_readable_name).', ';
                                        });
                                    ?>
                                </td>
                                <td><button data-user-id="{{$user->id}}"type="button" class="btn btn-danger remove"><i class="glyphicon glyphicon-trash"></i></button></td>
                            </tr>
                        @empty
                        @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    @foreach($users as $user)
        <form action="{{route('cooperation.admin.cooperation.cooperation-admin.users.destroy')}}" method="post" id="user-form-{{$user->id}}">
            {{csrf_field()}}
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="user_id" value="{{$user->id}}">
        </form>
    @endforeach
@endsection


@push('js')
    <script>
        $(document).ready(function () {
            var table = $('table');

            table.DataTable({
                responsive: true
            });

            table.on('click', '.remove', function (event) {
                if (confirm("{{__('woningdossier.cooperation.admin.cooperation.cooperation-admin.users.destroy.warning')}}")) {

                    // submit the form, datatables strips non valid html.
                    var userId = $(this).data('user-id');
                    $('form#user-form-'+userId).submit();

                } else {
                    event.preventDefault();
                    return false;
                }
            })
        })
    </script>
@endpush

