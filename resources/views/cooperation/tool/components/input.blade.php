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
    <?php
    // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
    if (false !== strpos($userInputColumn, '.')) {
        $value = array_get($userInputValue, $userInputColumn);
    } else {
        $value = $userInputValue->$userInputColumn;
    }
    ?>
    @if(!is_null($value))
        @if(isset($needsFormat) && $needsFormat == true)
            <?php $decimals = $decimals ?? 1; ?>
            <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{\App\Helpers\NumberFormatter::format($value, $decimals)}}</a></li>
        @else
            <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$value}}</a></li>
        @endif
    @endif
@endforeach