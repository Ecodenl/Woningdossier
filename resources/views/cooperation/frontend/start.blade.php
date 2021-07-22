@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-screen h-screen flex justify-center items-center flex-col">
        <div class="bg-white rounded-3xl w-3/4 flex flex-wrap overflow-hidden min-h-15/20">
            <div class="p-10 xl:p-20 w-1/2 flex flex-col justify-between">
                {!! __('cooperation/frontend/tool.start-description') !!}

                <button class="btn btn-purple w-full xl:w-1/4 flex items-center justify-center mt-5">
                    @lang('default.start')
                    <i class="icon-sm icon-arrow-right-circle ml-5"></i>
                </button>
            </div>
            <div class="text-center w-1/2 relative bg-center bg-no-repeat bg-cover"
                 style="background-image: url('{{ asset('images/family.png') }}')">
                <i class="icon-hoomdossier-white absolute h-1/10 w-1/2 bottom-1/20" style="right: 12%"></i>
            </div>
        </div>
    </div>
@endsection