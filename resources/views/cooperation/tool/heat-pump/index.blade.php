@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.heat-pump.title'))


@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.heat-pump.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="start-information">
            <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump.title')</h4>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.current-gas-usage')</label>
                        <div class="input-group">
                            <span class="input-group-addon">gas</span>
                            <input type="text" id="gas_usage" class="form-control disabled" disabled="" value="{{isset(Auth::user()->energyHabit) ? Auth::user()->energyHabit->amount_gas : ''}}">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.gas-usage-for-heating')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="resident_count" class="form-control disabled" disabled="" value="{{isset(Auth::user()->energyHabit) ? Auth::user()->energyHabit->resident_count : ''}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.gas-usage-for-tapwater')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="resident_count" class="form-control disabled" disabled="" value="{{isset(Auth::user()->energyHabit) ? Auth::user()->energyHabit->resident_count : ''}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="intro-questions">
            <div class="row">
                <div id="painted-options" >
                    <div class="col-sm-6">
                        <div class="form-group add-space{{ $errors->has('high_efficiency_heat-pump_id') ? ' has-error' : '' }}">
                            <label for="high_efficiency_heat-pump_id" class=" control-label"><i data-toggle="collapse" data-target="#high-efficiency-heat-pump-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.heat-pump.heat-pump-type') </label>

                            <select id="high_efficiency_heat-pump_id" class="form-control" name="high_efficiency_heat-pump_id">
                                @foreach($heatpumpTypes as $heatpumpType)
                                    <option @if(old('high_efficiency_heat-pump_id') == $heatpumpType->id) selected @endif value="{{ $heatpumpType->id }}">{{ $heatpumpType->name }}</option>
                                @endforeach
                            </select>

                            <div id="high-efficiency-heat-pump-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                        </div>

                        @if ($errors->has('high_efficiency_heat-pump'))
                            <span class="help-block">
                                <strong>{{ $errors->first('high_efficiency_heat-pump') }}</strong>
                            </span>
                        @endif


                    </div>
                    <div class="col-sm-6">
                        <div class="form-group add-space{{ $errors->has('high_efficiency_heat-pump_placed_date') ? ' has-error' : '' }}">
                            <label for="high_efficiency_heat-pump_placed_date" class=" control-label"><i data-toggle="collapse" data-target="#high-efficiency-heat-pump-placed-date-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.heat-pump.heat-pump-placed-date') </label>

                            <input type="date" class="form-control" value="@if(old('high_efficiency_heat-pump_placed_date')) {{old('high_efficiency_heat-pump_placed_date')}} @endif" name="high_efficiency_heat-pump_placed_date">

                            <div id="high-efficiency-heat-pump-placed-date-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                        </div>

                        @if ($errors->has('high_efficiency_heat-pump_placed_date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('high_efficiency_heat-pump_placed_date') }}</strong>
                            </span>
                        @endif

                    </div>
                </div>

            </div>
        </div>
        <div id="indication-for-costs">
            <hr>
            <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.title')</h4>

            <div id="costs" class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.gas-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">m3 / @lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.year')</span>
                            <input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.co2-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">co<sup>2</sup> / @lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.year')</span>
                            <input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.gas-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.year')</span>
                            <input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                {{--<div class="col-sm-4">--}}
                    {{--<div class="form-group add-space">--}}
                        {{--<label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.indicative-replacement')</label>--}}
                        {{--<div class="input-group">--}}
                            {{--<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>--}}
                            {{--<input type="date" id="replacement_moment" class="form-control disabled" disabled="" >--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.indicative-costs')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="cost_indication" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.comparable-rate')</label>
                        <div class="input-group">
                            <span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.heat-pump.indication-for-costs.year')</span>
                            <input type="text" id="interest_comparable" class="form-control disabled" disabled="" value="0,0">
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{route('cooperation.tool.general-data.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection