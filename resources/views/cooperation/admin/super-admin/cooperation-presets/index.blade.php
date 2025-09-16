@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/cooperation-presets.index.title')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/cooperation-presets.index.table.columns.title')</th>
                    <th>@lang('cooperation/admin/super-admin/cooperation-presets.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cooperationPresets as $cooperationPreset)
                    <tr>
                        <td>{{ $cooperationPreset->title }}</td>
                        <td>
                            <a href="{{ route('cooperation.admin.super-admin.cooperation-presets.show', compact('cooperationPreset')) }}"
                               class="btn btn-blue">
                                @lang('cooperation/admin/super-admin/cooperation-presets.show.title')
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
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

