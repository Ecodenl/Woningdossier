<?php
// sort the incoming collection based on input source order
if (is_array($userInputValues)) {
    $userInputValues = collect($userInputValues);
}
$userInputValues = $userInputValues->sortBy(function ($a) {
    return $a->inputSource->order;
});
?>
@foreach($userInputValues as $userInputValue)
    @foreach($inputValues as $inputValue)
        <?php
            // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
            if (false !== strpos($userInputColumn, '.')) {
                $value = array_get($userInputValue, $userInputColumn);
            } else {
                $value = $userInputValue->$userInputColumn;
            }
            // note that value can be an array
                // currently only used by ventilation page
                if (is_array($value)){
                    // set valueS..
                    $values = $value;
                }

            if (array_key_exists('value', $inputValue->attributesToArray())) {
                $inputName = $inputValue->value;
            } else {
                $inputName = $inputValue->name;
            }

        ?>

        <?php // currently only used by ventilation page --> we don't have "Ventilation" models.. ?>
        @if(isset($values) && is_array($values))
            @foreach($values as $value)
                @if(!is_null($value) && $inputValue->$userInputColumn == $value)
                    <li class="change-input-value" data-input-source-short="{{$userInputValue->inputSource()->first()->short}}" data-input-value="{{ $inputValue->$userInputColumn }}"><a href="#">{{ $userInputValue->getInputSourceName() }}: {{ $inputName }}</a></li>
                @endif
            @endforeach
        @else
            @if(!is_null($value) && $inputValue->id == $value)
                <li class="change-input-value" data-input-source-short="{{$userInputValue->inputSource()->first()->short}}" data-input-value="{{ $inputValue->id }}"><a href="#">{{ $userInputValue->getInputSourceName() }}: {{ $inputName }}</a></li>
            @endif
        @endif

    @endforeach
@endforeach