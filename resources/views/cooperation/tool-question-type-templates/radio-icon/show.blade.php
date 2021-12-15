<div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row gap-4">
    @php
        $questionValues = \App\Helpers\QuestionValues\QuestionValue::getQuestionValues(
            $toolQuestion,
            $building,
            $masterInputSource,
            $cooperation
        );
    @endphp
    @foreach($questionValues as $toolQuestionValue)
        @php
            $id = $toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value'] ?? $toolQuestionValue['value'];
        @endphp
        <div class="radio-wrapper media-wrapper">
            <input type="radio"
                   id="{{$id}}"
                   wire:model="filledInAnswers.{{$toolQuestion['id']}}"
                   value="{{$toolQuestionValue['value']}}"
                   @if($disabled) disabled="disabled" @endif

            >
            <label for="{{$id}}">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon'] ?? ''}}"></i>
                            </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
            </label>
        </div>
    @endforeach
</div>