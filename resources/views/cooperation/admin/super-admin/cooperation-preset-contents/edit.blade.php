@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/cooperation-preset-contents.edit.title'),
])

@section('content')
    @php
        $view = "cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.{$cooperationPreset->short}.form";
    @endphp
    <livewire:dynamic-component :component="$view" :cooperation-preset="$cooperationPreset"
                                :cooperation-preset-content="$cooperationPresetContent"/>
@endsection

