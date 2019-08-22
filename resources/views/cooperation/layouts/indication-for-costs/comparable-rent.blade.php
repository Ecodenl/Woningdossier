<?php
if (isset($id)) {
    $infoAlertId = $id.'interest-comparable-info';
} else {
    $infoAlertId = 'interest-comparable-info';
}
?>

@component('cooperation.tool.components.step-question', ['id' => $infoAlertId, 'translation' => 'general.costs.comparable-rent'])
    <div class="input-group">
        <span class="input-group-addon">% / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}interest_comparable" class="form-control disabled"
               disabled="" value="0,0">
    </div>
@endcomponent
