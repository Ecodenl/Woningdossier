<?php
    $dismissible = isset($dismissible) ? $dismissible : true;
    $alertType = (isset($alertType) && in_array($alertType, ['success', 'info', 'warning', 'danger'])) ? $alertType : 'success';
    $hide = (isset($hide) && $hide) ? 'hide' : '';
    $id = isset($id) ? $id : '';
    // if its collapsable we also need a lill topspace
    $collapsable = (isset($collapsable) && $collapsable) ? 'collapse alert-top-space' : '';
    $classes = isset($classes) ? $classes : '';
?>

<div id="{{$id}}" class="alert {{$collapsable}} alert-{{ $alertType }} @if($dismissible)alert-dismissible @endif {{ $hide }} {{$classes}}" role="alert">
    @if($dismissible)
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    @endif
    {{$slot}}
</div>