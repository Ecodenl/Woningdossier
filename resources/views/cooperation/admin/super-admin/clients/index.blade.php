@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/clients.index.header'),
    'panelLink' => route('cooperation.admin.super-admin.clients.create')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/clients.column-translations.name')</th>
                    <th>@lang('cooperation/admin/super-admin/clients.index.table.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    <tr>
                        <td>{{$client->name}}</td>
                        <td>
                            <a href="{{route('cooperation.admin.super-admin.clients.edit', compact('cooperation', 'client'))}}"
                               class="btn btn-blue">
                                @lang('cooperation/admin/super-admin/clients.index.table.edit')
                            </a>
                            <a href="{{route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('cooperation', 'client'))}}"
                               class="btn btn-purple">
                                @lang('cooperation/admin/super-admin/clients.index.table.api-tokens')
                            </a>
                            @can('delete', $client)
                                <form action="{{route('cooperation.admin.super-admin.clients.destroy', compact('client'))}}"
                                      method="POST" class="pl-4 inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="destroy btn btn-red" type="submit">
                                        @lang('default.buttons.destroy')
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
            });
        });

        document.on('click', '.destroy', function (event) {
            if (! confirm('@lang('cooperation/admin/super-admin/clients.destroy.warning')')) {
                event.preventDefault();
                return false;
            }
        });
    </script>
@endpush
