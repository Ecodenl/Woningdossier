@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/tool-calculation-results.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/tool-calculation-results.index.table.columns.name')</th>
                    <th>@lang('cooperation/admin/super-admin/tool-calculation-results.index.table.columns.short')</th>
                    <th>@lang('cooperation/admin/super-admin/tool-calculation-results.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($toolCalculationResults as $toolCalculationResult)
                    <tr>
                        <td>{{$toolCalculationResult->name}}</td>
                        <td>{{$toolCalculationResult->short}}</td>
                        <td>
                            <a href="{{route('cooperation.admin.super-admin.tool-calculation-results.edit', compact('toolCalculationResult'))}}"
                               class="btn btn-blue">
                                @lang('cooperation/admin/super-admin/tool-calculation-results.index.table.columns.edit')
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
