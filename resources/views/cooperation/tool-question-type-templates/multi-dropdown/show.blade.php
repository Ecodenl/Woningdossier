@php
    $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $toolQuestion)
        ->forBuilding($building)
        ->forInputSource($masterInputSource)
        ->answers(collect($this->prepareAnswersForEvaluation()))
        ->withCustomEvaluation()
        ->getQuestionValues();
@endphp
@component('cooperation.frontend.layouts.components.alpine-select')
    <select multiple id="{{$toolQuestion->short}}" class="form-input hidden"
            wire:model="filledInAnswers.{{$toolQuestion->short}}">
        @foreach($questionValues as $toolQuestionValue)
            <option value="{{ $toolQuestionValue['value'] }}"
                    @if(! empty($toolQuestionValue['extra']['icon'])) data-icon="{{ $toolQuestionValue['extra']['icon'] }}" @endif>
                {{ $toolQuestionValue['name'] }}
            </option>
        @endforeach
    </select>
@endcomponent