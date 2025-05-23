@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/measure-categories.index.title'),
    'panelLink' => route('cooperation.admin.super-admin.measure-categories.create')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
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
                               class="btn btn-blue">
                                @lang('cooperation/admin/super-admin/measure-categories.edit.title')
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
                scrollX: true,
                language: {
                    url: '{{ asset('js/datatables-dutch.json') }}'
                },
                layout: {
                    bottomEnd: {
                        paging: {
                            firstLast: false
                        }
                    }
                },
            });
        });
    </script>
@endpush