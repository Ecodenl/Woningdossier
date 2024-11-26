@extends('cooperation.layouts.app')

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

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20">
        @if($menu)
            @include('cooperation.admin.layouts.parts.sidebar-menu')
        @endif

        <div @class([
                 'border border-solid border-blue-500 border-opacity-50 rounded-lg',
                 'w-full' => ! $menu,
                 'w-10/12' => $menu
             ])
        >
            <div class="w-full divide-y divide-blue-500 divide-opacity-50">
                @if(! empty($panelTitle))
                    <div class="p-4 flex justify-between">
                        <h3 class="heading-5 inline-block">
                            {{ $panelTitle }}
                        </h3>
                    </div>
                @endif

                <div class="p-4" id="main-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection