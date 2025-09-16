@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/questionnaires.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/questionnaires.index.table.columns.questionnaire-name')</th>
                    <th>@lang('cooperation/admin/super-admin/questionnaires.index.table.columns.step')</th>
                    <th>@lang('cooperation/admin/super-admin/questionnaires.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questionnaires as $questionnaire)
                    <tr>
                        <td>{{$questionnaire->name}}</td>
                        <td>{{$questionnaire->step?->name}}</td>
                        <td>
                            <a href="{{route('cooperation.admin.super-admin.questionnaires.show', compact('questionnaire'))}}"
                               class="btn btn-blue">
                                @lang('cooperation/admin/super-admin/questionnaires.index.table.columns.copy')
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

