<?php
    if(isset($id)) {
        $infoAlertId = $id.'_co2-info';
    } else {
        $infoAlertId = 'co2-info';
    }
    
    // if the step is not given, fallback to the default translation
    if (!isset($step)) {
        $step = 'general';
    }
?>
<div class="form-group add-space">
    <label class="control-label">
        <i data-toggle="collapse" data-target="#{{$infoAlertId}}" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
        {{\App\Helpers\Translation::translate($step.'.costs.co2.title')}}
    </label>
    <div class="input-group">
        <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.kg.title') }} / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_co2" class="form-control disabled" disabled="" value="0">
    </div>
    @component('cooperation.tool.components.alert', ['alertType' => 'info', 'id' => $infoAlertId, 'collapsable' => true,])
        {{\App\Helpers\Translation::translate($step.'.costs.co2.help')}}
    @endcomponent
</div>
