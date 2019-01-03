<?php
// sort the incoming collection based on input source order
if (is_array($userInputValues)){
	$userInputValues = collect($userInputValues);
}
$userInputValues = $userInputValues->sortBy(function($a){
	return $a->inputSource->order;
});
?>
@foreach($userInputValues as $userInputValue)
    @foreach($inputValues as $inputValue)
        <?php
            // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
            if (strpos($userInputColumn, ".") !== false) {
                $value = array_get($userInputValue, $userInputColumn);
            } else {
                $value = $userInputValue->$userInputColumn;
            }

            if (array_key_exists('value', $inputValue->attributesToArray())) {
                $inputName = $inputValue->value;
            } else {
                $inputName = $inputValue->name;
            }
        ?>
        @if(!is_null($value) && $inputValue->id == $value)
            <li class="change-input-value" data-input-value="{{ $inputValue->id }}"><a href="#">{{ $userInputValue->getInputSourceName() }}: {{ $inputName }}</a></li>
        @endif
    @endforeach
@endforeach