<?php
if (isset($id)) {
    $infoAlertId = $id . 'indicative-costs-info';
} else {
    $infoAlertId = 'indicative-costs-info';
}
?>
@component('cooperation.tool.components.step-question', ['id' => $infoAlertId, 'translation' => 'general.costs.indicative-costs'])
    <div class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}cost_indication" class="form-control disabled" disabled=""
               value="0">
    </div>
@endcomponent