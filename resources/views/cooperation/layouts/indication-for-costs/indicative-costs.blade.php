<?php
if (isset($id)) {
    $infoAlertId = $id . 'indicative-costs-info';
} else {
    $infoAlertId = 'indicative-costs-info';
}
?>
@component('cooperation.tool.components.step-question', ['id' => $infoAlertId, 'translation' => $translation])
    <span class="input-group-prepend"><i class="icon-sm icon-moneybag"></i></span>
    <input type="text" id="{{isset($id) ? $id.'_' : ''}}cost_indication" class="form-input disabled" disabled=""
           value="0">
@endcomponent