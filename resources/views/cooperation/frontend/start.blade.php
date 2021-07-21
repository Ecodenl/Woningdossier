@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-screen h-screen flex justify-center items-center flex-col"
         style="background: url('{{ asset('images/background.jpg') }}')">
        <div class="bg-white rounded-3xl w-3/4 flex flex-wrap">
            <div class="p-20 w-1/2 flex flex-col justify-between">
                {!! __('cooperation/frontend/tool.start-description') !!}

                <button class="btn btn-purple w-1/4 flex items-center justify-center">
                    @lang('default.start')
                    <i class="icon-sm icon-arrow-right-circle ml-5"></i>
                </button>
            </div>
            <div class="text-center w-1/2 relative">
                <img src="{{ asset('images/family.png') }}" class="w-full max-h-full">
                <i class="icon-hoomdossier-white absolute h-1/10 w-1/2 bottom-1/20" style="right: 12%"></i>
            </div>
        </div>
    </div>
@endsection