@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading flex">
            @lang('cooperation/admin/super-admin/municipalities.index.title')
            <a href="{{route('cooperation.admin.super-admin.municipalities.create')}}"
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
                                <th>@lang('cooperation/admin/super-admin/municipalities.index.table.columns.name')</th>
                                <th>@lang('cooperation/admin/super-admin/municipalities.index.table.columns.bag')</th>
                                <th>@lang('cooperation/admin/super-admin/municipalities.index.table.columns.vbjehuis')</th>
                                <th>@lang('cooperation/admin/super-admin/municipalities.index.table.columns.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($municipalities as $municipality)
                                <tr>
                                    <td>{{ $municipality->name }}</td>
                                    <td>
                                        {{ implode(', ', $bagMunicipalities[$municipality->id] ?? []) }}
                                    </td>
                                    <td>
                                        {{ $vbjehuisMunicipalities[$municipality->id]['Name'] ?? null }}
                                    </td>
                                    <td>
                                        <a href="{{ route('cooperation.admin.super-admin.municipalities.edit', compact('municipality')) }}"
                                           class="btn btn-default">
                                            @lang('cooperation/admin/super-admin/municipalities.edit.title')
                                        </a>
                                        <a href="{{ route('cooperation.admin.super-admin.municipalities.show', compact('municipality')) }}"
                                           class="btn btn-success">
                                            @lang('cooperation/admin/super-admin/municipalities.show.title')
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
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            $('table').dataTable({
                responsive: false
            });
        });
    </script>
@endpush

