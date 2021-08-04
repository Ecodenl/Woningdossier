@extends('cooperation.frontend.layouts.app')

@section('header')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.navbar')
        @if(\App\Helpers\Blade\RouteLogic::inQuickScanTool(\Illuminate\Support\Facades\Route::currentRouteName()))
            {{-- Step progress --}}
            <div class="flex items-center justify-between w-full bg-blue-100 border-b-1 h-16 px-5 xl:px-20 relative z-30">
                <div class=" flex items-center h-full">
                    <i class="icon-sm icon-check-circle-dark mr-1"></i>
                    <span class="text-blue">Woninggegevens</span>
                </div>
                <div class="step-divider-line"></div>
                <div class="flex items-center h-full">
                    <i class="icon-sm bg-purple bg-opacity-25 rounded-full border border-solid border-purple mr-1"></i>
                    <span class="text-purple">Gebruik</span>
                </div>
                <div class="step-divider-line"></div>
                <div class="flex items-center h-full">
                    <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
                    <span class="text-blue">Woonwensen</span>
                </div>
                <div class="step-divider-line"></div>
                <div class="flex items-center h-full">
                    <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
                    <span class="text-blue">Woonstatus</span>
                </div>
                <div class="step-divider-line"></div>
                <div class="flex items-center h-full">
                    <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
                    <span class="text-blue">Overige</span>
                </div>
                <div class="border border-blue-500 border-opacity-50 h-1/2"></div>
                <div class="flex items-center justify-start h-full">
                    <i class="icon-sm icon-house-dark mr-1"></i>
                    <span class="text-blue">Woonplan</span>
                </div>
            </div>
            {{-- Progress bar --}}
            <div class="w-full bg-gray h-2">
                {{-- Define style-width based on step progress divided by total steps --}}
                <div class="h-full bg-purple" style="width: 30%"></div>
            </div>
        @endif
    </div>
@endsection

{{-- Remove BG image --}}
@section('main_style', '')

@section('main')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20">
        @yield('content')
    </div>
@endsection