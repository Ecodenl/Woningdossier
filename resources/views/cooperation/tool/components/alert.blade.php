<?php
    $dismissible = isset($dismissible) ? $dismissible : true;
    $alertType = (isset($alertType) && in_array($alertType, ['success', 'info', 'warning', 'danger'])) ? $alertType : 'success';
    $hide = (isset($hide) && $hide) ? 'hide' : '';
    $id = isset($id) ? $id : '';
    $collapsable = (isset($collapsable) && $collapsable) ? 'collapse' : '';
?>

<div id="{{$id}}" class="alert {{$collapsable}} alert-{{ $alertType }} @if($dismissible)alert-dismissible @endif {{ $hide }}" role="alert">
    @if($dismissible)
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    @endif
    {{$slot}}
</div>