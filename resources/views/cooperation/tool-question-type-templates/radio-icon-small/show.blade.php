{{-- Wraps: with the labels above A there are thirteen options, more than fit on one line. --}}
<div class="w-full flex flex-wrap gap-2">
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
        <div class="radio-wrapper media-wrapper media-wrapper-small">
            <input type="radio"
                   id="{{$uuid}}"
                   wire:model.live="filledInAnswers.{{$toolQuestion->short}}"
                   value="{{$toolQuestionValue['value']}}"
                   @if($disabled) disabled="disabled" @endif
            >
            <label for="{{$uuid}}">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon']}}"></i>
                            </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
                @if(! empty($toolQuestionValue['extra']['caption']))
                    <span class="media-caption">{{ $toolQuestionValue['extra']['caption'] }}</span>
                @endif
            </label>
        </div>
    @endforeach
</div>
