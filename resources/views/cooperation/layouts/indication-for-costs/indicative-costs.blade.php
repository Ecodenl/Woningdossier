<div class="form-group add-space">
    <label class="control-label">
        <i data-toggle="collapse" data-target="#{{isset($id) ? $id.'_' : ''}}indicative-costs-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
        {{\App\Helpers\Translation::translate('general.costs.indicative-costs.title')}}
    </label>
    <div class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}cost_indication" class="form-control disabled" disabled="" value="0">
    </div>
    <div id="{{isset($id) ? $id.'_' : ''}}indicative-costs-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
        {{\App\Helpers\Translation::translate('general.costs.indicative-costs.help')}}
    </div>
</div>