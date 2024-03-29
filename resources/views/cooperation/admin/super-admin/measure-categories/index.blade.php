@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading flex">
            @lang('cooperation/admin/super-admin/measure-categories.index.title')
            <a href="{{route('cooperation.admin.super-admin.measure-categories.create')}}"
               class="btn btn-md btn-primary">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>@lang('cooperation/admin/super-admin/measure-categories.index.table.columns.name')</th>
                                <th>@lang('cooperation/admin/super-admin/measure-categories.index.table.columns.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($measureCategories as $measureCategory)
                                <tr>
                                    <td>{{ $measureCategory->name }}</td>
                                    <td>
                                        <a href="{{ route('cooperation.admin.super-admin.measure-categories.edit', compact('measureCategory')) }}"
                                           class="btn btn-default">
                                            @lang('cooperation/admin/super-admin/measure-categories.edit.title')
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