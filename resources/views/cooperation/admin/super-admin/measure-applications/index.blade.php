@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/measure-applications.index.title')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/measure-applications.index.table.columns.name')</th>
                    <th>@lang('cooperation/admin/super-admin/measure-applications.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($measureApplications as $measureApplication)
                    <tr>
                        <td>{{$measureApplication->measure_name}}</td>
                        <td>
                            <a href="{{route('cooperation.admin.super-admin.measure-applications.edit', compact('measureApplication'))}}"
                               class="btn btn-blue">
                                @lang('cooperation/admin/super-admin/measure-applications.edit.label')
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
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