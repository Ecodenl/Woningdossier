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
        $uuid = Str::uuid();
    @endphp
    <div class="radio-wrapper pr-3">
        <input type="radio"
               id="{{$uuid}}"
               wire:model="filledInAnswers.{{$toolQuestion->id}}"
               value="{{$toolQuestionValue['value']}}"
               @if($disabled) disabled="disabled" @endif
        >
        <label for="{{$uuid}}">
            <span class="checkmark"></span>
            <span>{{$toolQuestionValue['name']}}</span>
        </label>
    </div>
@endforeach
