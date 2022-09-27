<div class="w-full flex justify-between">
    @php
        $questionValues = \App\Helpers\QuestionValues\QuestionValue::getQuestionValues(
            $toolQuestion,
            $building,
            $masterInputSource,
            collect($this->prepareAnswersForEvaluation())
        );
    @endphp
    @foreach($questionValues as $toolQuestionValue)
        @php
            $uuid = Str::uuid();
        @endphp
        <div class="radio-wrapper media-wrapper media-wrapper-small">
            <input type="radio"
                   id="{{$uuid}}"
                   wire:model="filledInAnswers.{{$toolQuestion['short']}}"
                   value="{{$toolQuestionValue['value']}}"
                   @if($disabled) disabled="disabled" @endif
            >
            <label for="{{$uuid}}">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon']}}"></i>
                            </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
            </label>
        </div>
    @endforeach
</div>
