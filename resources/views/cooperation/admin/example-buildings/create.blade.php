@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.example-buildings.create.header')
])

@section('content')
    <div class="flex w-full">
        <livewire:cooperation.admin.example-buildings.form/>
    </div>
@endsection