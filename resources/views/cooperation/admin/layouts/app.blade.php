@extends('cooperation.layouts.app')

@section('base_css')
    @vite('resources/css/admin/app.css')
@endsection

@section('header')
    <div class="w-full">
        @include('cooperation.admin.layouts.parts.navbar')
    </div>
@endsection

{{-- Remove BG image --}}
@section('main_style', '')

@section('main')
    @php
        // Whether or not to show the side menu
        $menu ??= true;
    @endphp

    <div class="@if(! $menu) max-w-7xl @endif mx-auto sm:px-6 lg:px-8 pt-8 flex flex-wrap justify-between mb-10">
        @include('cooperation.layouts.parts.messages')

        @if(str_contains(Route::currentRouteName(), 'cooperation-to-manage'))
            <div class="w-full flex flex-wrap justify-between xl:mb-4">
                <div class="w-full xl:w-3/20 mb-2 xl:mb-0">
                    <a class="btn btn-purple text-center"
                       href="{{route('cooperation.admin.super-admin.cooperations.index', ['search' => $cooperationToManage?->name])}}">
                        @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.back-to-normal-environment')
                    </a>
                </div>
                <div class="w-full xl:w-10/12">
                    @component('cooperation.layouts.components.alert', [
                        'color' => 'yellow',
                        'class' => 'mt-0',
                        'dismissible' => false,
                    ])
                        <strong>@lang('default.caution')</strong> @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.alert-on-top', ['cooperation' => $cooperationToManage->name])
                    @endcomponent
                </div>
            </div>
        @endif

        @if($menu)
            @include('cooperation.admin.layouts.parts.sidebar-menu')
        @endif

        <div @class([
                 'border border-solid border-blue-500 border-opacity-50 rounded-lg',
                 'w-full' => ! $menu,
                 'w-full xl:w-10/12' => $menu
             ])
        >
            <div class="w-full divide-y divide-blue-500/50">
                @if(! empty($panelTitle))
                    <div class="p-4 flex items-center justify-between">
                        <h3 class="heading-5 inline-block font-normal">
                            {!! $panelTitle !!}
                        </h3>
                        @if(! empty($panelLink))
                            <a href="{{ $panelLink }}" class="h-10">
                                <i class="w-10 h-10 icon-plus-circle"></i>
                            </a>
                        @endif
                    </div>
                @endif

                <div class="p-4" id="main-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @vite('resources/js/datatables.js')
@endpush