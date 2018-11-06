{{-- TODO: could use some improvement, do not know how atm--}}
@if(is_array($inputValues) && $customInputValueColumn == false)
    @foreach($inputValues as $key => $inputValue)
        @foreach($userInputValues as $userInputValue)
            {{--we use array get, we cant use it like $userInputValue->$userInputColumn--}}
            <?php
            // check if the input column has dots, ifso we need to use the array get function
            // else its a property that we can access
            if (strpos($userInputColumn, '.') !== false) {
                $compareValue = array_get($userInputValue, $userInputColumn);
            } else {
                $compareValue = $userInputValue->$userInputColumn;
            }
            ?>
            @if($key == $compareValue)
                <li class="change-input-value" data-input-value="{{$key}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$inputValue}}</a></li>
            @endif
        @endforeach
    @endforeach
@else
    @foreach($inputValues as $inputValue)
        @foreach($userInputValues as $userInputValue)
            <?php
            if ($userInputModel instanceof \Illuminate\Database\Eloquent\Model) {
                $value = $userInputValue->$userInputModel->$userInputColumn;
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
            @if($inputValue->id == $value)
                <li class="change-input-value" data-input-value="{{$inputValue->id}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$inputName}}</a></li>
            @endif
        @endforeach
    @endforeach
@endif