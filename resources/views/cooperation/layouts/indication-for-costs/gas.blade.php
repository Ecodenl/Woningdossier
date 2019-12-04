<?php
if (isset($id)) {
    $infoAlertId = $id . '_gas-info';
} else {
    $infoAlertId = 'gas-info';
}
// if the step is not given, fallback to the default translation
if (!isset($step)) {
    $step = 'general';
}
?>
    @component('cooperation.tool.components.step-question', ['id' => $infoAlertId, 'translation' => $step.'.costs.gas'])
        <div class="input-group">
            <span class="input-group-addon">m<sup>3</sup> / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
            <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_gas" class="form-control disabled" disabled=""
                   value="0">
        </div>
    @endcomponent