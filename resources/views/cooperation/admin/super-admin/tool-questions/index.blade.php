@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/tool-questions.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/tool-questions.index.table.columns.name')</th>
                    <th>@lang('cooperation/admin/super-admin/tool-questions.index.table.columns.short')</th>
                    <th>@lang('cooperation/admin/super-admin/tool-questions.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($toolQuestions as $toolQuestion)
                    <tr>
                        <td>{{$toolQuestion->name}}</td>
                        <td>{{$toolQuestion->short}}</td>
                        <td>
                            <a href="{{route('cooperation.admin.super-admin.tool-questions.edit', compact('toolQuestion'))}}"
                               class="btn btn-blue">
                                @lang('cooperation/admin/super-admin/tool-questions.index.table.columns.edit')
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

