@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/cooperation-presets.index.title')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive ">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/super-admin/cooperation-presets.index.table.columns.title')</th>
                            <th>@lang('cooperation/admin/super-admin/cooperation-presets.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cooperationPresets as $cooperationPreset)
                            <tr>
                                <td>{{$cooperationPreset->title}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.super-admin.cooperation-presets.show', compact('cooperationPreset'))}}"
                                       class="btn btn-success">
                                        @lang('cooperation/admin/super-admin/cooperation-presets.show.title')
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

