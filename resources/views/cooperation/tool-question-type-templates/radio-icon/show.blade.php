<div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row gap-4">
    @php
        $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $toolQuestion)
            ->forInputSource($masterInputSource)
            ->forBuilding($building)
            ->answers(collect($filledInAnswers))
            ->withCustomEvaluation()
            ->getQuestionValues();
    @endphp
    @foreach($questionValues as $toolQuestionValue)
        @php
            $uuid = Str::uuid();
        @endphp
        <div class="radio-wrapper media-wrapper">
            <input type="radio"
                   id="{{$uuid}}"
                   wire:model="filledInAnswers.{{$toolQuestion['short']}}"
                   value="{{$toolQuestionValue['value']}}"
                   @if($disabled) disabled="disabled" @endif

            >
            <label for="{{$uuid}}">
                <span class="media-icon-wrapper">
                    <i class="{{$toolQuestionValue['extra']['icon'] ?? ''}}"></i>
                </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
            </label>
        </div>
    @endforeach
</div>