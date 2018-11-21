@foreach($userInputValues as $userInputValue)
    <?php
    // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
    if (strpos($userInputColumn, ".") !== false) {
        $value = array_get($userInputValue, $userInputColumn);
    } else {
        $value = $userInputValue->$userInputColumn;
    }
    ?>
    @if(isset($needsFormat) && $needsFormat == true)
        <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{\App\Helpers\NumberFormatter::format($value, 1)}}</a></li>
    @else
        <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$value}}</a></li>
    @endif
@endforeach