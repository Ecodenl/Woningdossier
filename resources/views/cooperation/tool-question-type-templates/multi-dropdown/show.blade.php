@php
    $questionValues = \App\Helpers\QuestionValues\QuestionValue::getQuestionValues(
        $toolQuestion,
        $building,
        $masterInputSource,
        collect($this->prepareAnswersForEvaluation())
    );
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