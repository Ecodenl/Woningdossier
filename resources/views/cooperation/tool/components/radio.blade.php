@if(is_array($inputValues))
    @foreach($inputValues as $inputValue)
        @foreach($userInputValues as $userInputValue)
            <?php
                // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
                if (strpos($userInputColumn, ".") !== false) {
                    $value = array_get($userInputValue, $userInputColumn);
                } else {
                    $value = $userInputValue->$userInputColumn;
                }
            ?>
            @if($inputValue == $value)
                <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$inputValue}}</a></li>
            @endif
        @endforeach
    @endforeach
@endif
