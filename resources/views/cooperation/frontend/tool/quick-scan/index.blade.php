@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
        <div class="w-full">
            @livewire('cooperation.frontend.tool.quick-scan.form', compact('step', 'subStep'))
        </div>
    </div>

    <div class="w-full flex flex-wrap items-center">
        <div class="w-1/4 flex flex-wrap justify-start"></div>
        @livewire('cooperation.frontend.tool.quick-scan.buttons', compact('step', 'subStep'))
        <div class="w-1/4 flex flex-wrap justify-end">
            <p>
                {!! __('cooperation/frontend/tool.step-count', ['current' => '<span class="font-bold">' . $current .'</span>', 'total' => $total]) !!}
            </p>
        </div>
    </div>
@endsection