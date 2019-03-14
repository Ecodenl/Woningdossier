@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('heat-pump.title.title'))


@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.heat-pump.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="start-information">
            @include('cooperation.tool.includes.section-title', [
               'translation' => 'heat-pump.description',
                'id' => 'heat-pump-title'
             ])
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space">
                        <div class="panel panel-primary">
                            <div class="panel-heading">@lang('woningdossier.cooperation.tool.heat-pump-information.downloads.title')</div>
                            <div class="panel-body">
                                <ol>
                                    <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Warmtepomp.pdf')}}">{{ucfirst(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/maatregelblad_warmtepomp.pdf'))))}}</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>


                {{--<div id="current-gas-usage">--}}

                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.net-gas-usage')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>--}}
                                {{--<input type="text" id="net-gas-usage" class="form-control disabled" disabled="" value="">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.energy-content')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h / m<sup>3</sup></span>--}}
                                {{--<input type="text" id="energy-content" class="form-control disabled" disabled="" >--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.heat')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h</span>--}}
                                {{--<input type="text" id="heat" class="form-control disabled" disabled="" >--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}
            {{--<div class="row">--}}
                {{--<div class="col-sm-12">--}}
                    {{--<div class="form-group add-space">--}}
                        {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.gas-usage-for-heating')</label>--}}
                        {{--<div class="input-group">--}}
                            {{--<span class="input-group-addon"><i class="glyphicon glyphicon-fire"></i></span>--}}
                            {{--<input type="text" id="resident_count" class="form-control disabled" disabled="" value="{{isset(Auth::user()->energyHabit) ? Auth::user()->energyHabit->resident_count : ''}}">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}


                {{--<div id="gas-usage-for-warm-tapwater">--}}

                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.net-gas-usage')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>--}}
                                {{--<input type="text" id="net-gas-usage" class="form-control disabled" disabled="" value="">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.energy-content')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h / m<sup>3</sup></span>--}}
                                {{--<input type="text" id="energy-content" class="form-control disabled" disabled="" >--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.heat')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h</span>--}}
                                {{--<input type="text" id="heat" class="form-control disabled" disabled="" >--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="row">--}}
                {{--<div class="col-sm-12">--}}
                    {{--<div class="form-group add-space{{ $errors->has('high_efficiency_heat-pump_id') ? ' has-error' : '' }}">--}}
                        {{--<label for="high_efficiency_heat-pump_id" class=" control-label"><i data-toggle="modal" data-target="#high-efficiency-heat-pump-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.heat-pump.heat-pump-type') </label>--}}

                        {{--<select id="high_efficiency_heat-pump_id" class="form-control" name="high_efficiency_heat-pump_id">--}}
                            {{--@foreach($heatpumpTypes as $heatpumpType)--}}
                                {{--<option @if(old('high_efficiency_heat-pump_id') == $heatpumpType->id) selected @endif value="{{ $heatpumpType->id }}">{{ $heatpumpType->name }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}

                        {{--<div id="high-efficiency-heat-pump-info" class="collapse alert alert-info remove-collapse-space alert-top-space">--}}
                            {{--And i would like to have it to...--}}
                        {{--</div>--}}

                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<div id="hybrid-heatpump">--}}
            {{--<div id="hybrid-heatpump-indication-for-costs">--}}
                {{--<hr>--}}
                {{--<h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.title')</h4>--}}
                {{--<div class="row" id="heat-usage-heater">--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.heat')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.cop')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">COP</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.electro-usage-heatpump')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h / @lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<h5 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.title')</h5>--}}

                {{--<div id="costs" class="row">--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.gas-savings')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">m3 / @lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.electro-usage-heatpump')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">m3 / @lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.co2-savings')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">co<sup>2</sup> / @lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.gas-savings')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.moreusage-electro-in-euro')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.saldo')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.indicative-costs')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>--}}
                                {{--<input type="text" id="cost_indication" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.comparable-rate')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.heat-pump.hybrid-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="interest_comparable" class="form-control disabled" disabled="" value="0,0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<div id="full-heatpump">--}}

            {{--<div id="full-heatpump-indication-for-costs">--}}
                {{--<hr>--}}
                {{--<h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.title')</h4>--}}
                {{--<div class="row">--}}
                    {{--<div class="col-sm-6">--}}
                        {{--<div class="form-group add-space{{ $errors->has('current_building_heating') ? ' has-error' : '' }}">--}}
                            {{--<label for="current_building_heating" class=" control-label"><i data-toggle="modal" data-target="#current-building-heating-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.current-heating') </label>--}}

                            {{--<select id="current_building_heating" class="form-control" name="current_building_heating">--}}
                                {{--@foreach($buildingCurrentHeatings as $buildingCurrentHeating)--}}
                                    {{--<option @if(old('current_building_heating') == $buildingCurrentHeating->id) selected @endif value="{{ $buildingCurrentHeating->id }}">{{ $buildingCurrentHeating->name }}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}

                            {{--<div id="current-building-heating-info" class="collapse alert alert-info remove-collapse-space alert-top-space">--}}
                                {{--And i would like to have it to...--}}
                            {{--</div>--}}

                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-6">--}}
                        {{--<div class="form-group add-space{{ $errors->has('wanted_heat_source') ? ' has-error' : '' }}">--}}
                            {{--<label for="wanted_heat_source" class=" control-label"><i data-toggle="modal" data-target="#wanted-heat-source-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.wanted-heat-source') </label>--}}

                            {{--<select id="wanted-heat-source" class="form-control" name="wanted_heat_source">--}}
                                {{--@foreach($heatSources as $heatSource)--}}
                                    {{--<option @if(old('wanted_heat_source') == $heatSource->id) selected @endif value="{{ $heatSource->id }}">{{ $heatSource->name }}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}

                            {{--<div id="wanted-heat-source-info" class="collapse alert alert-info remove-collapse-space alert-top-space">--}}
                                {{--And i would like to have it to...--}}
                            {{--</div>--}}

                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="row">--}}
                    {{--<div class="col-sm-12 col-md-8 col-md-offset-2">--}}
                        {{--<div class="alert alert-info show" role="alert">--}}
                            {{--<p id="wanted-heat-source-text"></p>--}}
                            {{--<p id="insulation-advice"></p>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.heat-usage.heater')</h4>--}}
                {{--<div class="row" id="heat-usage-heater-heater">--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.heat')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.cop')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">COP</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.electro-usage-heatpump')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.heat-usage.warm-tapwater')</h4>--}}
                {{--<div class="row" id="heat-usage-heater-warm-tapwater">--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.heat')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.cop')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">COP</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.electro-usage-heatpump')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">k<sup>W</sup>h / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<h5 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.title')</h5>--}}

                {{--<div id="costs" class="row">--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.gas-savings')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">m3 / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.electro-usage-heatpump')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">m3 / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.co2-savings')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">co<sup>2</sup> / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.gas-savings')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.moreusage-electro-in-euro')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.saldo')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.indicative-costs')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>--}}
                                {{--<input type="text" id="cost_indication" class="form-control disabled" disabled="" value="0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<div class="form-group add-space">--}}
                            {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.comparable-rate')</label>--}}
                            {{--<div class="input-group">--}}
                                {{--<span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.heat-pump.full-heatpump.indication-for-costs.year')</span>--}}
                                {{--<input type="text" id="interest_comparable" class="form-control disabled" disabled="" value="0,0">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        @if(\App\Helpers\HoomdossierSession::isUserNotObserving())
        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{route('cooperation.tool.high-efficiency-boiler.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.next-page')
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </form>
@endsection

@push('js')
    <script>
        // var gasUsageTapWater = $('#gas-usage-for-warm-tapwater');
        // var currentGasUsage = $('#current-gas-usage');
        //
        // $(document).ready(function () {
        //     $('#wanted-heat-source').change( function () {
        //         // Get the selected option
        //         var wantedHeatSource = $('#wanted-heat-source option:selected').text();
        //         // Get the current text and change it
        //         $('#wanted-heat-source-text').text('Warmtepomp met '+ wantedHeatSource.toLowerCase()+' al warmtebron');
        //
        //     });
        //
        //     $('#wanted-heat-source').trigger('change');
        // });
    </script>
@endpush