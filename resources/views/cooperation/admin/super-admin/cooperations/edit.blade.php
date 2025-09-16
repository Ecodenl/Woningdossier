@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/cooperations.edit.title', ['name' => $cooperationToEdit->name])
])

@section('content')
    <div class="flex w-full">
        <livewire:cooperation.admin.super-admin.cooperations.form :cooperationToEdit="$cooperationToEdit"/>
    </div>
@endsection