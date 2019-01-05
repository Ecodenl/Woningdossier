<?php
    if (isset($id)) {
        $infoAlertId = $id.'_gas-info';
    } else {
        $infoAlertId = 'gas-info';
    }
    // if the step is not given, fallback to the default translation
    if (! isset($step)) {
        $step = 'general';
    }
?>
<div class="form-group add-space">
    <label class="control-label">
        <i data-toggle="collapse" data-target="#{{$infoAlertId}}" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
        {{\App\Helpers\Translation::translate($step.'.costs.gas.title')}}
    </label>
    <div class="input-group">
        <span class="input-group-addon">m<sup>3</sup> / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_gas" class="form-control disabled" disabled="" value="0">
    </div>
    @component('cooperation.tool.components.alert', ['alertType' => 'info', 'id' => $infoAlertId, 'collapsable' => true,])
        {{\App\Helpers\Translation::translate($step.'.costs.gas.help')}}
    @endcomponent
</div>