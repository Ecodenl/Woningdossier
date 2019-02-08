<?php
    // where we will putt the answer by input source id
    $userInputValuesArray = [];

    // loop trough it, explode it and put it in the array
    foreach ($userInputValues as $userInputValue) {
        $userInputValuesArray[] = [
            'answers' => collect(explode('|', $userInputValue->answer)),
            'input_source' => $userInputValue->inputSource
        ];
    }

?>

@foreach($userInputValuesArray as $userInputValue)
    @foreach ($inputValues as $inputValue)
        @if ($userInputValue['answers']->contains($inputValue->id))
            <li class="change-input-value" data-input-source-short="{{$userInputValue['input_source']->short}}" data-input-value="{{ $inputValue->id }}"><a href="#">{{ $userInputValue['input_source']->name }}: {{ $inputValue->name }}</a></li>
        @endif
    @endforeach
@endforeach
