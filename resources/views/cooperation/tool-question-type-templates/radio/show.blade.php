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
    <div class="radio-wrapper pr-3">
        <input type="radio"
               id="{{$uuid}}"
               wire:model="filledInAnswers.{{$toolQuestion->short}}"
               value="{{$toolQuestionValue['value']}}"
               @if($disabled) disabled="disabled" @endif
        >
        <label for="{{$uuid}}">
            <span class="checkmark"></span>
            <span>{{$toolQuestionValue['name']}}</span>
        </label>
    </div>
@endforeach
