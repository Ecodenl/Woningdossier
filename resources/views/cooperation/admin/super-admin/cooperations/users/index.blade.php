@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.header'),
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.created-at')</th>
                    <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.name')</th>
                    <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.email')</th>
                    <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index.table.actions')</th>
                </tr>
            </thead>
            <tbody>
                @php
                    /** @var \App\Models\User $user */
                @endphp
                @foreach($users as $user)
                    <tr>
                        <td data-sort="{{$user->created_at instanceof \Carbon\Carbon ? strtotime($user->created_at->format('d-m-Y')) : '-'}}">
                            {{$user->created_at instanceof \Carbon\Carbon ? $user->created_at->format('d-m-Y') : '-'}}
                        </td>
                        <td>{{$user->getFullName()}}</td>
                        <td>{{$user->account->email}}</td>
                        <td>
                            <a class="btn btn-outline-blue inline-flex items-center"
                               href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show', ['cooperationToManage' => $cooperationToManage,'user' => $user->id,])}}"
                            >
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
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
                scrollX: true,
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