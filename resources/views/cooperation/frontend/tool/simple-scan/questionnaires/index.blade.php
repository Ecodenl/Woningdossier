@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full">
        <div class="w-full flex flex-wrap justify-between">
            <h4 class="heading-4">
                {{ $questionnaire->name }}
            </h4>
        </div>

        @include('cooperation.frontend.layouts.parts.custom-questionnaire', [
            'questionnaire' => $questionnaire, 'isTab' => false
        ])

        <div class="w-full flex flex-wrap items-center">
            <div class="w-1/4 flex flex-wrap justify-start"></div>
            <livewire:cooperation.frontend.tool.simple-scan.buttons :scan="$scan" :step="$step"
                                                                    :subStepOrQuestionnaire="$questionnaire"/>
            <div class="w-1/4 flex flex-wrap justify-end">
                <p>
                    {!! __('cooperation/frontend/tool.step-count', ['current' => '<span class="font-bold">' . $current .'</span>', 'total' => $total]) !!}
                </p>
            </div>
        </div>
    </div>
@endsection