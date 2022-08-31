@php
    $questionValues = \App\Helpers\QuestionValues\QuestionValue::getQuestionValues(
        $toolQuestion,
        $building,
        $masterInputSource,
        $cooperation
    );
@endphp
@component('cooperation.frontend.layouts.components.alpine-select')
    <select multiple id="{{$toolQuestion->short}}" class="form-input" wire:model="filledInAnswers.{{$toolQuestion->id}}">
        @foreach($questionValues as $toolQuestionValue)
            <option value="{{ $toolQuestionValue['value'] }}">
                {{ $toolQuestionValue['name'] }}
            </option>
        @endforeach
    </select>
@endcomponent