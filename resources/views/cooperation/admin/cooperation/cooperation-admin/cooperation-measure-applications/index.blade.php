@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.index.title'),
    'panelLink' => route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.create', compact('type')),
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.index.table.columns.name')</th>
                    <th>@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.index.table.columns.icon')</th>
                    <th>@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cooperationMeasureApplications as $cooperationMeasureApplication)
                    <tr>
                        <td>{{$cooperationMeasureApplication->name}}</td>
                        <td>
                            <i class="icon-lg {{$cooperationMeasureApplication->extra['icon'] ?? 'icon-tools'}}"></i>
                        </td>
                        <td>
                            <a class="btn btn-blue table-cell"
                               href="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.edit', compact('cooperationMeasureApplication'))}}">
                                @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.edit.label')
                            </a>

                            @can('delete', $cooperationMeasureApplication)
                                <form action="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.destroy', compact('cooperationMeasureApplication'))}}"
                                      method="POST" class="pl-2 table-cell">
                                    @csrf
                                    @method('DELETE')

                                    <button class="destroy btn btn-red" type="submit">
                                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.label')
                                    </button>
                                </form>
                            @endcan
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

        document.on('click', '.destroy', function (event) {
            if (! confirm('@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.destroy.warning')')) {
                event.preventDefault();
                return false;
            }
        });
    </script>
@endpush
