@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/users.index.header')
])

@section('content')
    <div class="flex w-full">
        @include('cooperation.admin.super-admin.users.search')
    </div>
@endsection