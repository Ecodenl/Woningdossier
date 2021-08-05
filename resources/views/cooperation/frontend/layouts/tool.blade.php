@extends('cooperation.frontend.layouts.app')

@section('header')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.navbar')
        {{-- Step progress --}}
        @include('cooperation.frontend.layouts.parts.sub-nav')
    </div>
@endsection

{{-- Remove BG image --}}
@section('main_style', '')

@section('main')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20">
        @yield('content')
    </div>
@endsection