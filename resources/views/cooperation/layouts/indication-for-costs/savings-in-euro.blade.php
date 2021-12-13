<?php
if (isset($id)) {
    $infoAlertId = $id . 'year-info';
} else {
    $infoAlertId = 'year-info';
}
?>
@component('cooperation.tool.components.step-question', [
    'id' => $infoAlertId, 'translation' => $translation, 'withInputSource' => false,
])
    <span class="input-group-prepend"><i class="icon-sm icon-moneybag mr-1"></i> / @lang('general.unit.year.title')</span>
    <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_money" class="form-input disabled" disabled=""
           value="0">
@endcomponent