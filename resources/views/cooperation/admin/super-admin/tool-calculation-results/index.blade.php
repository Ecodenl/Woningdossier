@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/tool-calculation-results.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive ">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/super-admin/tool-calculation-results.index.table.columns.name')</th>
                            <th>@lang('cooperation/admin/super-admin/tool-calculation-results.index.table.columns.short')</th>
                            <th>@lang('cooperation/admin/super-admin/tool-calculation-results.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($toolCalculationResults as $toolCalculationResult)
                            <tr>
                                <td>{{$toolCalculationResult->name}}</td>
                                <td>{{$toolCalculationResult->short}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.super-admin.tool-calculation-results.edit', compact('toolCalculationResult'))}}" class="btn btn-default">@lang('cooperation/admin/super-admin/tool-calculation-results.index.table.columns.edit')</a>
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
            $('table').dataTable({
                responsive: false
            });
        });
    </script>
@endpush

