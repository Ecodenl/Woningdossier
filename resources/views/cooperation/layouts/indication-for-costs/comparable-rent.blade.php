<?php
if (isset($id)) {
    $infoAlertId = $id . 'interest-comparable-info';
} else {
    $infoAlertId = 'interest-comparable-info';
}
?>

@component('cooperation.tool.components.step-question', [
    'id' => $infoAlertId, 'translation' => $translation, 'withInputSource' => false,
])
    <span class="input-group-prepend">% / @lang('general.unit.year.title')</span>
    <input type="text" id="{{isset($id) ? $id.'_' : ''}}interest_comparable" class="form-input disabled"
           disabled="" value="0,0">
@endcomponent
