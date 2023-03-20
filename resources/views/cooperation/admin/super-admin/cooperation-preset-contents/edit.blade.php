@extends('cooperation.admin.layouts.app')

@section('content')
    <section class="section">
        <div class="container">
            @php
                $view = "cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.{$cooperationPreset->short}.form";
            @endphp
            <livewire:dynamic-component :component="$view" :cooperation-preset="$cooperationPreset"
                                        :cooperation-preset-content="$cooperationPresetContent"/>
        </div>
    </section>
@endsection

