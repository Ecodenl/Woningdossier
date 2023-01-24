<?php
// sort the incoming collection based on input source order
if (is_array($userInputValues)) {
    $userInputValues = collect($userInputValues);
}
$userInputValues = $userInputValues->sortBy(function ($a) {
    return $a->inputSource->order;
});
?>
@if(is_array($inputValues))
    @foreach($userInputValues as $userInputValue)
        @foreach($inputValues as $inputValue => $inputValueLabel)
            <?php
                // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
                if (false !== strpos($userInputColumn, '.')) {
                    $value = Arr::get($userInputValue, $userInputColumn);
                } else {
                    $value = $userInputValue->$userInputColumn;
                }
            ?>
            @if(!is_null($value) && $inputValue == $value)
                <li class="change-input-value" x-on:click="changeOption($el)"
                    data-input-value="{{ $value }}"
                    data-input-source-short="{{$userInputValue->inputSource->short}}">
                    {{ $userInputValue->getInputSourceName() }}: {{ $inputValueLabel }}
                </li>
            @endif
        @endforeach
    @endforeach
@endif
