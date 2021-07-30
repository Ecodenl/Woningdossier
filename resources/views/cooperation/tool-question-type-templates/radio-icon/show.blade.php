{{--<div class="w-full grid grid-rows-1 grid-cols-8 grid-flow-row gap-4 ">--}}
<div class="w-full flex justify-start space-x-8">
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
