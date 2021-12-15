@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/questionnaires.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive ">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/super-admin/questionnaires.index.table.columns.questionnaire-name')</th>
                            <th>@lang('cooperation/admin/super-admin/questionnaires.index.table.columns.step')</th>
                            <th>@lang('cooperation/admin/super-admin/questionnaires.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($questionnaires as $questionnaire)
                            <tr>
                                <td>{{$questionnaire->name}}</td>
                                <td>{{optional($questionnaire->step)->name}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.super-admin.questionnaires.show', compact('questionnaire'))}}" class="btn btn-default">@lang('cooperation/admin/super-admin/questionnaires.index.table.columns.copy')</a>
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

