<?php
if (isset($id)) {
    $infoAlertId = $id . '_gas-info';
} else {
    $infoAlertId = 'gas-info';
}
// if the step is not given, fallback to the default translation
if (! isset($step)) {
    $step = 'general';
}
?>
@component('cooperation.tool.components.step-question', ['id' => $infoAlertId, 'translation' => $translation])
    <span class="input-group-prepend">m<sup>3</sup> / @lang('general.unit.year.title')</span>
    <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_gas" class="form-input disabled" disabled=""
           value="0">
@endcomponent