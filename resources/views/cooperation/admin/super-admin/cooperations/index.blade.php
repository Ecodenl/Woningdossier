@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/cooperations.index.title'),
    'panelLink' => route('cooperation.admin.super-admin.cooperations.create')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
            <tr>
                <th>@lang('cooperation/admin/super-admin/cooperations.index.table.columns.name')</th>
                <th>@lang('cooperation/admin/super-admin/cooperations.index.table.columns.slug')</th>
                <th>@lang('cooperation/admin/super-admin/cooperations.index.table.columns.actions')</th>
            </tr>
            </thead>
            <tbody>
                @foreach($cooperations as $cooperation)
                    <tr>
                        <td>{{$cooperation->name}}</td>
                        <td>{{$cooperation->slug}}</td>
                        <td>
                            <a href="{{route('cooperation.admin.super-admin.cooperations.edit', ['cooperation' => $currentCooperation, 'cooperationToEdit' => $cooperation])}}"
                               class="btn btn-blue">
                                @lang('default.buttons.edit')
                            </a>
                            <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', ['cooperation' => $currentCooperation, 'cooperationToManage' => $cooperation])}}"
                               class="btn btn-purple ml-1">
                                @lang('cooperation/admin/super-admin/cooperations.show.title')
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
                // responsive: true,
                // columnDefs: [
                //     {responsivePriority: 2, targets: 1},
                //     {responsivePriority: 1, targets: 0}
                // ],
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
                search: {
                    search: '{{ request()->input('search') }}'
                }
            });
        });
    </script>
@endpush
