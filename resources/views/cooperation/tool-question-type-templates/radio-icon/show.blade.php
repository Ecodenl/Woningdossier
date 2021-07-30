<div class="
@if($toolQuestions->count() > 1)
w-full flex justify-between
@else
w-full grid grid-rows-2 grid-cols-4 grid-flow-row gap-4
@endif
">
    @foreach($toolQuestion->getQuestionValues() as $toolQuestionValue)
        <div class="radio-wrapper media-wrapper @if($toolQuestions->count() > 1) media-wrapper-small @endif">
            <input type="radio"
                   id="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}"
                   wire:model="filledInAnswers.{{$toolQuestion['id']}}"
                   value="{{$toolQuestionValue['short'] ?? $toolQuestionValue['id']}}"
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
