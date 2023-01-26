@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/cooperation-preset-contents.edit.title')
        </div>

        @php
            $view = "cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.{$cooperationPreset->short}.form";
        @endphp
        <livewire:dynamic-component :component="$view" :cooperation-preset-content="$cooperationPresetContent"/>
    </div>
@endsection

