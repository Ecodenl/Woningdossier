@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/tool-questions.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive ">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/super-admin/tool-questions.index.table.columns.name')</th>
                            <th>@lang('cooperation/admin/super-admin/tool-questions.index.table.columns.short')</th>
                            <th>@lang('cooperation/admin/super-admin/tool-questions.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($toolQuestions as $toolQuestion)
                            <tr>
                                <td>{{$toolQuestion->name}}</td>
                                <td>{{$toolQuestion->short}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.super-admin.tool-questions.edit', compact('toolQuestion'))}}" class="btn btn-default">@lang('cooperation/admin/super-admin/tool-questions.index.table.columns.edit')</a>
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

