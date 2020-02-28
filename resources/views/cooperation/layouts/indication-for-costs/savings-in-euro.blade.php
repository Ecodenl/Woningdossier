<?php
if (isset($id)) {
    $infoAlertId = $id . 'year-info';
} else {
    $infoAlertId = 'year-info';
}
?>
@component('cooperation.tool.components.step-question', ['id' => $infoAlertId, 'translation' => $translation])
    <div class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i> / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_money" class="form-control disabled" disabled=""
               value="0">
    </div>
@endcomponent