@php
    $questionValues = \App\Helpers\QuestionValues\QuestionValue::getQuestionValues(
        $toolQuestion,
        $building,
        $masterInputSource,
        $cooperation
    );
@endphp
@component('cooperation.frontend.layouts.components.alpine-select')
    <select id="{{$toolQuestion->short}}" class="form-input hidden" wire:model="filledInAnswers.{{$toolQuestion->id}}">
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