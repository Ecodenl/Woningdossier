<?php
    if(isset($id)) {
        $infoAlertId = $id.'indicative-costs-info';
    } else {
        $infoAlertId = 'indicative-costs-info';
    }
?>
<div class="form-group add-space">
    <label class="control-label">
        <i data-toggle="collapse" data-target="#{{$infoAlertId}}" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
        {{\App\Helpers\Translation::translate('general.costs.indicative-costs.title')}}
    </label>
    <div class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}cost_indication" class="form-control disabled" disabled="" value="0">
    </div>
    @component('cooperation.tool.components.alert', ['alertType' => 'info', 'id' => $infoAlertId, 'collapsable' => true])
        {{\App\Helpers\Translation::translate('general.costs.indicative-costs.help')}}
    @endcomponent
</div>