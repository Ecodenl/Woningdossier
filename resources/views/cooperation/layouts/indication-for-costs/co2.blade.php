<?php
if (isset($id)) {
    $infoAlertId = $id . '_co2-info';
} else {
    $infoAlertId = 'co2-info';
}

// if the step is not given, fallback to the default translation
if (! isset($step)) {
    $step = 'general';
}
?>
@component('cooperation.tool.components.step-question', [
    'id' => $infoAlertId, 'translation' => $translation, 'withInputSource' => false,
])
    <span class="input-group-prepend">@lang('general.unit.kg.title') / @lang('general.unit.year.title')</span>
    <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_co2" class="form-input disabled" disabled=""
           value="0">
@endcomponent
