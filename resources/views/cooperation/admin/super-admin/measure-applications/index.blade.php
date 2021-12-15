@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/measure-applications.index.title')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive ">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/super-admin/measure-applications.index.table.columns.name')</th>
                            <th>@lang('cooperation/admin/super-admin/measure-applications.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($measureApplications as $measureApplication)
                            <tr>
                                <td>{{$measureApplication->measure_name}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.super-admin.measure-applications.edit', compact('measureApplication'))}}"
                                       class="btn btn-success">
                                        @lang('cooperation/admin/super-admin/measure-applications.edit.label')
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
            $('table').dataTable({
                responsive: false
            });
        });
    </script>
@endpush

