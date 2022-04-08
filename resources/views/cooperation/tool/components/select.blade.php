<?php
    // sort the incoming collection based on input source order
    if (is_array($userInputValues)) {
        $userInputValues = collect($userInputValues);
    }
    $userInputValues = $userInputValues->sortBy(function ($a) {
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
            if (false !== strpos($userInputColumn, '.')) {
                $compareValue = Arr::get($userInputValue, $userInputColumn);
            } else {
                $compareValue = $userInputValue->$userInputColumn;
            }
            ?>
            @if(!is_null($compareValue) && $key == $compareValue)
                <li class="change-input-value" data-input-value="{{ $key }}" data-input-source-short="{{$userInputValue->inputSource->short}}">{{ $userInputValue->getInputSourceName() }}: {{ $inputValue }}</li>
            @endif
        @endforeach
    @endforeach
@else
    @foreach($userInputValues as $userInputValue)
        @foreach($inputValues as $inputValue)
            <?php
            if ($userInputModel instanceof \Illuminate\Database\Eloquent\Model) {
                if (! $userInputValue->$userInputModel instanceof \Illuminate\Database\Eloquent\Model) {
                    $value = null;
                } else {
                    $value = $userInputValue->$userInputModel->$userInputColumn;
                }
            } else {
                if (false !== strpos($userInputColumn, '.')) {
                    $value = Arr::get($userInputValue, $userInputColumn);
                } else {
                    $value = $userInputValue->$userInputColumn;
                }
            }

            if (isset($customInputValueColumn) && true == $customInputValueColumn) {
                $inputName = $inputValue->$customInputValueColumn;
            } elseif (array_key_exists('value', $inputValue->attributesToArray())) {
                $inputName = $inputValue->value;
            } else {
                $inputName = $inputValue->name;
            }
            ?>
            @if(!is_null($value) && $inputValue->id == $value)
                <li class="change-input-value" data-input-value="{{ $inputValue->id }}" data-input-source-short="{{$userInputValue->inputSource->short}}">{{ $userInputValue->getInputSourceName() }}: {{ $inputName }}</li>
            @endif
        @endforeach
    @endforeach
@endif