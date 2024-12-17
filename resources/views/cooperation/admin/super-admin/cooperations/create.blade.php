@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/cooperations.create.title')
])

@section('content')
    <div class="flex w-full">
        <livewire:cooperation.admin.super-admin.cooperations.form/>
    </div>
@endsection
