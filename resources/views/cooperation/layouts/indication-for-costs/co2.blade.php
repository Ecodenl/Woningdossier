
<div class="form-group add-space">
    <label class="control-label">
        <i data-toggle="collapse" data-target="#{{isset($id) ? $id.'_' : ''}}co2-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
        {{\App\Helpers\Translation::translate('general.costs.co2.title')}}
    </label>
    <div class="input-group">
        <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.kg.title') }} / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_co2" class="form-control disabled" disabled="" value="0">
    </div>
    <div id="{{isset($id) ? $id.'_' : ''}}co2-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
        {{\App\Helpers\Translation::translate('general.costs.co2.help')}}
    </div>
</div>
