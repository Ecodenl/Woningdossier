@php
    $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $toolQuestion)
        ->forInputSource($masterInputSource)
        ->forBuilding($building)
        ->answers(collect($this->prepareAnswersForEvaluation()))
        ->withCustomEvaluation()
        ->getQuestionValues();
@endphp
@component('cooperation.frontend.layouts.components.alpine-select')
    <select id="{{$toolQuestion->short}}" class="form-input hidden" wire:model="filledInAnswers.{{$toolQuestion->short}}">
        <option value="">
            @lang('default.form.dropdown.choose')
        </option>
        @foreach($questionValues as $toolQuestionValue)
            <option value="{{ $toolQuestionValue['value'] }}">
                {{ $toolQuestionValue['name'] }}
            </option>
        @endforeach
    </select>
@endcomponent