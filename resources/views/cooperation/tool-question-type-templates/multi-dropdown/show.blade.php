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
        <option value="">
            @lang('default.form.dropdown.choose')
        </option>
        @foreach($questionValues as $toolQuestionValue)
            <option value="{{ $toolQuestionValue['value'] }}"
                    @if(! empty($toolQuestionValue['extra']['icon'])) data-icon="{{ $toolQuestionValue['extra']['icon'] }}" @endif>
                {{ $toolQuestionValue['name'] }}
            </option>
        @endforeach
    </select>
@endcomponent