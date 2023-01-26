@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading flex">
            @lang('cooperation/admin/super-admin/cooperation-presets.show.title')
            <a href="{{route('cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.create', compact('cooperationPreset'))}}"
               class="btn btn-md btn-primary">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive ">
                        <thead>
                            <tr>
                                <th>@lang('cooperation/admin/super-admin/cooperation-presets.show.table.columns.title')</th>
                                <th>@lang('cooperation/admin/super-admin/cooperation-presets.show.table.columns.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cooperationPreset->cooperationPresetContents as $cooperationPresetContent)
                                <tr>
                                    @php
                                        // We could do _another_ mapping, or we can just attempt a few columns, as we
                                        // mostly use the same names anyway
                                        $title = $cooperationPresetContent->content['title'] ?? $cooperationPresetContent->content['name'] ?? null;

                                        // Might be translatable
                                        if (is_array($title)) {
                                            $title = $title['nl'] ?? null;
                                        }
                                    @endphp

                                    <td>
                                        {{ $title }}
                                    </td>
                                    <td>
                                        <a href="{{route('cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.edit', compact('cooperationPreset', 'cooperationPresetContent'))}}"
                                           class="btn btn-success">
                                            @lang('cooperation/admin/super-admin/cooperation-preset-contents.edit.title')
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

