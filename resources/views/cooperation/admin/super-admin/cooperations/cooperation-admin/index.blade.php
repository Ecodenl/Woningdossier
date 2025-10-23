@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index.table.name')</th>
                    <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index.table.email')</th>
                    <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.actions')</th>
                </tr>
            </thead>
            <tbody>
                @php
                    /** @var \App\Models\User $user */
                @endphp
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->getFullName()}}</td>
                        <td>{{$user->account->email}}</td>
                        <td>
                            <a class="btn btn-outline-blue inline-flex items-center"
                               href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show', compact('cooperationToManage', 'user'))}}">
                                <i class="icon-md icon-tools"></i>
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
                order: [[0, "desc"]],
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