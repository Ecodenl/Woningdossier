@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.header')
            <a href="{{route('cooperation.admin.cooperation.coordinator.coach.create')}}" class="btn btn-md btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span></a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.first-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.last-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.role')</th>
{{--                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.actions')</th>--}}
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
                                            echo ucfirst($role->human_readable_name) .', ';
                                        })
                                    ?>
                                </td>
                                {{--<td>--}}
                                    {{--<form action="{{route('cooperation.admin.cooperation.coordinator.coach.destroy', ['userId' => $user->id])}}" method="post">--}}
                                        {{--{{csrf_field()}}--}}
                                        {{--<button type="submit" class="btn remove btn-danger"><span class="glyphicon glyphicon-trash"></span></button>--}}
                                    {{--</form>--}}
                                {{--</td>--}}
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
            $('table').DataTable();

            $('.remove').click(function () {
                if (confirm("Weet u zeker dat u de gebruiker wilt verwijderen")) {

                } else {
                    event.preventDefault();
                }
            })
        })
    </script>
@endpush

