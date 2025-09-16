@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/example-buildings.edit.title', ['name' => $exampleBuilding->name])
])

@section('content')
    <div class="flex w-full">
        <livewire:cooperation.admin.example-buildings.form :exampleBuilding="$exampleBuilding"/>
    </div>
@endsection
