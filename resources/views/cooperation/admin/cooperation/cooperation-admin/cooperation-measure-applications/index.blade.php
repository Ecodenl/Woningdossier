@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.index.title')
            <a href="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.create', compact('type'))}}"
               class="btn btn-md btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span></a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.index.table.columns.name')</th>
                            <th>@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.index.table.columns.icon')</th>
                            <th>@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cooperationMeasureApplications as $cooperationMeasureApplication)
                            <tr>
                                <td>{{$cooperationMeasureApplication->name}}</td>
                                <td>
                                    <i class="icon-xl {{$cooperationMeasureApplication->extra['icon'] ?? 'icon-tools'}}"></i>
                                </td>
                                <td>
                                    <a class="btn btn-success" style="display: table-cell;"
                                       href="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.edit', compact('cooperationMeasureApplication'))}}">
                                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.edit.label')
                                    </a>

                                    @can('delete', $cooperationMeasureApplication)
                                        <form action="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.destroy', compact('cooperationMeasureApplication'))}}"
                                              method="post" class="pl-10" style="display: table-cell;">
                                            @csrf
                                            @method('DELETE')

                                            <button class="destroy btn btn-danger" type="submit">
                                                @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.label')
                                            </button>
                                        </form>
                                    @endcan
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
            $('#table').dataTable();

            $(document).on('click', '.destroy', function (event) {
                if (! confirm('@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.warning')')) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endpush
