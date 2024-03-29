@extends('cooperation.frontend.layouts.tool')

@section('content')
    @if($activeNotification)
        <livewire:cooperation.frontend.layouts.parts.notifications
                :types="[App\Jobs\CloneOpposingInputSource::class]"
                :nextUrl="route('cooperation.frontend.tool.simple-scan.index', compact('scan', 'step', 'subStep'))"
        />
        @include('cooperation.frontend.shared.parts.loader', [
            'label' => __("cooperation/frontend/tool/simple-scan/index.cloning-in-progress.{$currentInputSource->short}")
        ])
    @else
        <div class="w-full">
            <livewire:cooperation.frontend.tool.simple-scan.form :scan="$scan" :step="$step" :subStep="$subStep"/>
        </div>

        <div class="w-full flex flex-wrap items-center">
            <div class="w-1/4 flex flex-wrap justify-start"></div>
            <livewire:cooperation.frontend.tool.simple-scan.buttons :scan="$scan" :step="$step" :subStepOrQuestionnaire="$subStep"/>
            <div class="w-1/4 flex flex-wrap justify-end">
                <p>
                    {!! __('cooperation/frontend/tool.step-count', ['current' => '<span class="font-bold">' . $current .'</span>', 'total' => $total]) !!}
                </p>
            </div>
        </div>
    @endif
@endsection