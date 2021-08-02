@component('cooperation.tool-question-type-templates.components.default')
<div class="w-full flex justify-between">
    @foreach($toolQuestion->getQuestionValues() as $toolQuestionValue)
        <div class="radio-wrapper media-wrapper media-wrapper-small">
            <input type="radio"
                   id="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}"
                   wire:model="filledInAnswers.{{$toolQuestion['id']}}"
                   value="{{$toolQuestionValue['value']}}"
            >
            <label for="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon']}}"></i>
                            </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
            </label>
        </div>
    @endforeach
</div>
@endcomponent
