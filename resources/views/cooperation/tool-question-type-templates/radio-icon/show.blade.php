<div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row gap-4">
    @foreach($toolQuestion->getQuestionValues() as $toolQuestionValue)
        <div class="radio-wrapper media-wrapper">
            <input type="radio"
                   id="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}"
                   wire:model="filledInAnswers.{{$toolQuestion['id']}}"
                   value="{{$toolQuestionValue['value']}}"
            >
            <label for="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon'] ?? ''}}"></i>
                            </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
            </label>
        </div>
    @endforeach
</div>