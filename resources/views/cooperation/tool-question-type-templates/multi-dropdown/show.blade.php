@php

    $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $toolQuestion)
   ->forBuilding($building)
            ->forInputSource($masterInputSource)
            ->answers(collect($this->prepareAnswersForEvaluation()))
            ->withCustomEvaluation()
            ->getQuestionValues();
@endphp
@component('cooperation.frontend.layouts.components.alpine-select')
    <select multiple id="{{$toolQuestion->short}}" class="form-input hidden" data-livewire-id="{{$this->id}}"
            x-on:component-ready.window="if ($event.detail.id == $el.getAttribute('data-livewire-id')) { constructSelect(); }"
            wire:model="filledInAnswers.{{$toolQuestion->short}}"
            @if(($disabled ?? false))
                disabled
            @else
                x-on:input-updated.window="$el.setAttribute('disabled', true);"
                x-on:input-update-processed.window="$el.removeAttribute('disabled');"
            @endif>
        @foreach($questionValues as $toolQuestionValue)
            <option value="{{ $toolQuestionValue['value'] }}"
                    @if(! empty($toolQuestionValue['extra']['icon'])) data-icon="{{ $toolQuestionValue['extra']['icon'] }}" @endif>
                {{ $toolQuestionValue['name'] }}
            </option>
        @endforeach
    </select>
@endcomponent