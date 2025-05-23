@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/municipalities.index.title'),
    'panelLink' => route('cooperation.admin.super-admin.municipalities.create')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
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
                               class="btn btn-blue mb-2 mr-2">
                                @lang('cooperation/admin/super-admin/municipalities.edit.title')
                            </a>
                            <a href="{{ route('cooperation.admin.super-admin.municipalities.show', compact('municipality')) }}"
                               class="btn btn-yellow">
                                @lang('cooperation/admin/super-admin/municipalities.show.title')
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
                scrollX: true,
                responsive: false,
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

