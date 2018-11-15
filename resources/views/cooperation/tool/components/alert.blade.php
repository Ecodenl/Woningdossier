<?php
    $dismissible = isset($dismissible) ? $dismissible : true;
    $alertType = (isset($alertType) && in_array($alertType, ['success', 'info', 'warning', 'danger'])) ? $alertType : 'success';
?>

<div class="alert alert-{{ $alertType }} @if($dismissible)alert-dismissible @endif show" role="alert">
    @if($dismissible)
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    @endif
    {{$slot}}
</div>