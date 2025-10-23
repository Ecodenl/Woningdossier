@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/clients/personal-access-tokens.index.header', ['client_name' => $client->name]),
    'panelLink' => route('cooperation.admin.super-admin.clients.personal-access-tokens.create', compact('client'))
])

@section('content')
    <div class="w-full data-table">
        @if(session()->has('token'))
            <p>@lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.token-created')</p>
            @component('cooperation.layouts.components.alert', ['color' => 'green', 'dismissible' => false])
                {{session('token')->plainTextToken}}
            @endcomponent
        @endif
        <table id="table" class="table fancy-table">


            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.name')</th>
                    <th>@lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.last-used')</th>
                    <th>@lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->tokens as $personalAccessToken)
                    <tr>
                        <td>{{$personalAccessToken->name}}</td>
                        <td>{{$personalAccessToken->last_used_at}}</td>
                        <td>
                            <form class="inline"
                                  action="{{route('cooperation.admin.super-admin.clients.personal-access-tokens.destroy', compact('client', 'personalAccessToken'))}}"
                                  method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-red destroy">
                                    @lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.delete')
                                </button>
                            </form>

                            <a class="btn btn-blue"
                               href="{{route('cooperation.admin.super-admin.clients.personal-access-tokens.edit', compact('client', 'personalAccessToken'))}}">
                                @lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.edit')
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
            if (! confirm('@lang('cooperation/admin/super-admin/clients/personal-access-tokens.destroy.confirm')')) {
                event.preventDefault();
                return false;
            }
        });
    </script>
@endpush
