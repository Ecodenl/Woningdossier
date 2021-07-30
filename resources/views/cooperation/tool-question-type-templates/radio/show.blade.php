@foreach($toolQuestion->getQuestionValues() as $toolQuestionValue)

    <div class="radio-wrapper pr-3">
        <input type="radio"
               id="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}"
               wire:model="filledInAnswers.{{$toolQuestion['id']}}"
               value="{{$toolQuestionValue['short'] ?? $toolQuestionValue['id']}}"
        >
        <label for="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}">
            <span class="checkmark"></span>
            <span>{{$toolQuestionValue['name']}}</span>
        </label>
    </div>
@endforeach