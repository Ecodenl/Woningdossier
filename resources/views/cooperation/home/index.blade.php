@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-screen h-screen flex justify-center items-center flex-col">
        <div class="bg-white rounded-3xl w-3/4 flex flex-wrap overflow-hidden min-h-15/20">
            <div class="p-10 xl:p-20 w-1/2 flex flex-col justify-between">
                @if(session('verified'))
                    @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800'])
                        @lang('cooperation/auth/verify.success-log-in')
                    @endcomponent
                @endif
                {!! __('home.start.description') !!}

                <a class="btn btn-purple w-full xl:w-1/4 flex items-center justify-center mt-5"
                   <?php
                   $step = \App\Models\Step::whereShort('building-data')->first();
                   $subStep = \App\Models\SubStep::first();
                   ?>
                    href="{{route('cooperation.quick-scan.index', ['step' => $step->slug, 'subStep' => $subStep->slug])}}">
                    @lang('default.start')
                    <i class="icon-sm icon-arrow-right-circle ml-5"></i>
                </a>
            </div>
            <div class="text-center w-1/2 relative bg-center bg-no-repeat bg-cover"
                 style="background-image: url('{{ asset('images/family.png') }}')">
                <i class="icon-hoomdossier-white absolute h-1/10 w-1/2 bottom-1/20" style="right: 12%"></i>
            </div>
        </div>
    </div>
@endsection