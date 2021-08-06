@extends('cooperation.frontend.layouts.app')

@section('header')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.navbar')
        {{-- Step progress --}}
        @include('cooperation.frontend.layouts.parts.sub-nav')
        {{-- Progress bar --}}
        <div class="w-full bg-gray h-2">
            @php
                $total = $total ?? 100;
                $current = $current ?? 100;
                $width = 100 / $total * $current;
            @endphp
            {{-- Define style-width based on step progress divided by total steps --}}
            <div class="h-full bg-purple" style="width: {{$width}}%"></div>
        </div>
    </div>
@endsection

{{-- Remove BG image --}}
@section('main_style', '')

@section('main')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20">
        @livewire('cooperation.frontend.tool.quick-scan.my-plan.form')
{{--        @yield('content')--}}
    </div>
@endsection

