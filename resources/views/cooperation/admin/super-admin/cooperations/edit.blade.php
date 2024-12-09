@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.super-admin.cooperations.edit.header', ['name' => $cooperationToEdit->name])
])

@section('content')
    <div class="flex w-full">
        <livewire:cooperation.admin.super-admin.cooperations.form :cooperationToEdit="$cooperationToEdit"/>
    </div>
@endsection