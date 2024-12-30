@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/users.index.header')
])

@section('content')
    <div class="w-full">
        @include('cooperation.admin.super-admin.users.search')
    </div>

    @if($users->isNotEmpty())
        <div class="w-full mt-5">
            <table id="table" class="table fancy-table">
                <thead>
                    <tr>
                        <th>@lang('cooperation/admin/super-admin/users.show.table.columns.cooperation')</th>
                        <th>@lang('cooperation/admin/super-admin/users.show.table.columns.email')</th>
                        <th>@lang('cooperation/admin/super-admin/users.show.table.columns.name')</th>
                        <th>@lang('cooperation/admin/super-admin/users.show.table.columns.street-house-number')</th>
                        <th>@lang('cooperation/admin/super-admin/users.show.table.columns.zip-code')</th>
                        <th>@lang('cooperation/admin/super-admin/users.show.table.columns.city')</th>
                        <th>@lang('cooperation/admin/super-admin/users.show.table.columns.roles')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php
                            $building = $user->building;
                        @endphp
                        <tr>
                            <td>
                                {{$user->cooperation->name}}
                            </td>
                            <td>
                                {{$user->account->email}}
                            </td>
                            <td>
                                {{$user->getFullName()}}
                            </td>
                            <td>
                                <a class="in-text"
                                   href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show', ['cooperationToManage' => $user->cooperation, 'user' => $user->id])}}">
                                    {{$building->street}} {{$building->number}} {{$building->extension}}
                                </a>
                            </td>
                            <td>{{$building->postal_code}}</td>
                            <td>
                                {{$building->city}}
                            </td>
                            <td>
                                {{$user->roles->pluck('human_readable_name')->implode(', ')}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

@if($users->isNotEmpty())
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
@endif