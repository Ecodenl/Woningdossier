<?php
    if (isset($id)) {
        $infoAlertId = $id.'interest-comparable-info';
    } else {
        $infoAlertId = 'interest-comparable-info';
    }
?>
<div class="form-group add-space">
    <label class="control-label">
        <i data-toggle="modal" data-target="#{{$infoAlertId}}" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
        {{\App\Helpers\Translation::translate('general.costs.comparable-rent.title')}}
    </label>
    <div class="input-group">
        <span class="input-group-addon">% / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}interest_comparable" class="form-control disabled" disabled="" value="0,0">
    </div>
    @component('cooperation.tool.components.help-modal')
        {{\App\Helpers\Translation::translate('general.costs.comparable-rent.help')}}
    @endcomponent
</div>
