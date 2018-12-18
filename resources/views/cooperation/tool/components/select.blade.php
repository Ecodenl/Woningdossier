<?php
    // sort the incoming collection based on input source order
    $userInputValues = $userInputValues->sortBy(function($a){
	    return $a->inputSource->order;
    });
?>
@if(is_array($inputValues) && $customInputValueColumn == false)
    @foreach($userInputValues as $userInputValue)
        @foreach($inputValues as $key => $inputValue)
            {{-- We use array get, we cant use it like $userInputValue->$userInputColumn --}}
            <?php
            // check if the input column has dots, ifso we need to use the array get function
            // else its a property that we can access
            if (strpos($userInputColumn, '.') !== false) {
                $compareValue = array_get($userInputValue, $userInputColumn);
            } else {
                $compareValue = $userInputValue->$userInputColumn;
            }
            ?>
            @if(!is_null($compareValue) && $key == $compareValue)
                <li class="change-input-value" data-input-value="{{ $key }}"><a href="#">{{ $userInputValue->getInputSourceName() }}: {{ $inputValue }}</a></li>
            @endif
        @endforeach
    @endforeach
@else
    @foreach($userInputValues as $userInputValue)
        @foreach($inputValues as $inputValue)
            <?php
            if (isset($userInputModel) && $userInputModel == true) {
            	if (!$userInputValue->$userInputModel instanceof \Illuminate\Database\Eloquent\Model){
            		$value = null;
                }
            	else {
                    $value = $userInputValue->$userInputModel->$userInputColumn;
            	}
            } else {
                if (strpos($userInputColumn, ".") !== false) {
                    $value = array_get($userInputValue, $userInputColumn);
                } else {
                    $value = $userInputValue->$userInputColumn;
                }
            }

            if (isset($customInputValueColumn) && $customInputValueColumn == true) {
                $inputName = $inputValue->$customInputValueColumn;
            } else if (array_key_exists('value', $inputValue->attributesToArray())) {
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
@endif