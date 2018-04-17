@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.solar-panels.title'))


@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.solar-panels.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="solar-panels">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.solar-panels.title')</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('user_energy_habits.amount_electricity') ? ' has-error' : '' }}">
                        <label for="user_energy_habits_amount_electricity" class=" control-label"><i data-toggle="collapse" data-target="#user-energy-habits-amount-electricity-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.electricity-consumption-past-year')</label>

                        <input type="text" class="form-control" name="user_energy_habits[amount_electricity]" value="{{ old('user_energy_habits.amount_electricity', $amountElectricity) }}" />

                        <div id="user-energy-habits-amount-electricity-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                    </div>

                    @if ($errors->has('user_energy_habits.amount_electricity'))
                        <span class="help-block">
                            <strong>{{ $errors->first('user_energy_habits.amount_electricity') }}</strong>
                        </span>
                    @endif

                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('building_pv_panels.peak_power') ? ' has-error' : '' }}">
                        <label for="building_pv_panels_peak_power" class=" control-label"><i data-toggle="collapse" data-target="#peak-power-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.solar-panels.peak-power')</label>


                        <select id="building_pv_panels_peak_power" class="form-control" name="building_pv_panels[peak_power]">
                            @foreach(range(260, 300, 5) as $peakPower)
                                <option @if(old('building_pv_panels.peak_power') == $peakPower || ($buildingPvPanels instanceof \App\Models\BuildingPvPanel && $buildingPvPanels->peak_power == $peakPower)) selected @endif value="{{ $peakPower }}">{{ $peakPower }}</option>
                            @endforeach
                        </select>

                        <div id="peak-power-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                    </div>

                    @if ($errors->has('building_pv_panels.peak_power'))
                        <span class="help-block">
                            <strong>{{ $errors->first('building_pv_panels.peak_power') }}</strong>
                        </span>
                    @endif

                </div>
            </div>

            <div class="row advice">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert alert-info show" role="alert">
                        <p id="solar-panels-advice"></p>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('building_pv_panels.number') ? ' has-error' : '' }}">
                        <label for="building_pv_panels_number" class=" control-label"><i data-toggle="collapse" data-target="#number-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.solar-panels.number')</label>

                        <input type="text" class="form-control" name="building_pv_panels[number]" value="{{ old('building_pv_panels.number', $buildingPvPanels instanceof \App\Models\BuildingPvPanel ? $buildingPvPanels->number : 0) }}" />

                        <div id="number-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                    </div>

                    @if ($errors->has('building_pv_panels.number'))
                        <span class="help-block">
                            <strong>{{ $errors->first('building_pv_panels.number') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('building_pv_panels.pv_panel_orientation_id') ? ' has-error' : '' }}">
                        <label for="building_pv_panels_pv_panel_orientation_id" class=" control-label"><i data-toggle="collapse" data-target="#orientation-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.solar-panels.pv-panel-orientation-id')</label>

                        <select id="building_pv_panels_pv_panel_orientation_id" class="form-control" name="building_pv_panels[pv_panel_orientation_id]">
                            @foreach($pvPanelOrientations as $pvPanelOrientation)
                                <option @if(old('building_pv_panels.pv_panel_orientation_id') == $pvPanelOrientation->id || ($buildingPvPanels instanceof \App\Models\BuildingPvPanel && $buildingPvPanels->pv_panel_orientation_id == $pvPanelOrientation->id)) selected @endif value="{{ $pvPanelOrientation->id }}">{{ $pvPanelOrientation->name }}</option>
                            @endforeach
                        </select>

                        <div id="orientation-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                    </div>

                    @if ($errors->has('building_pv_panels.pv_panel_orientation_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('building_pv_panels.pv_panel_orientation_id') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('building_pv_panels.angle') ? ' has-error' : '' }}">
                        <label for="building_pv_panels_angle" class=" control-label"><i data-toggle="collapse" data-target="#angle-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.solar-panels.angle')</label>


                        <select id="building_pv_panels_angle" class="form-control" name="building_pv_panels[angle]">
                            @foreach([10, 15, 20, 30, 40, 45, 50, 60, 70, 75, 90] as $angle)
                                <option @if(old('building_pv_panels.angle') == $angle || ($buildingPvPanels instanceof \App\Models\BuildingPvPanel && $buildingPvPanels->angle == $angle)) selected @endif value="{{ $angle }}">{{ $angle }}</option>
                            @endforeach
                        </select>

                        <div id="angle-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                    </div>

                    @if ($errors->has('building_pv_panels.angle'))
                        <span class="help-block">
                            <strong>{{ $errors->first('building_pv_panels.angle') }}</strong>
                        </span>
                    @endif
                </div>

            </div>

            <div class="row total-power">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert alert-info show" role="alert">
                        <p id="solar-panels-total-power"></p>
                    </div>
                </div>
            </div>

            <div id="indication-for-costs">
                <hr>
                <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.title')</h4>

                <div id="costs" class="row">
                    <div class="col-sm-4">
                        <div class="form-group add-space">
                            <label class="control-label">@lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.yield-electricity')</label>
                            <div class="input-group">
                                <span class="input-group-addon">kWh / @lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.year')</span>
                                <input type="text" id="yield_electricity" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group add-space">
                            <label class="control-label">@lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.raise-own-consumption')</label>
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input type="text" id="raise_own_consumption" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group add-space">
                            <label class="control-label">@lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.co2-savings')</label>
                            <div class="input-group">
                                <span class="input-group-addon">co<sup>2</sup> / @lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.year')</span>
                                <input type="text" id="savings_co2" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group add-space">
                            <label class="control-label">@lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.savings-in-euro')</label>
                            <div class="input-group">
                                <span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.year')</span>
                                <input type="text" id="savings_money" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group add-space">
                            <label class="control-label">@lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.indicative-costs')</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="cost_indication" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group add-space">
                            <label class="control-label">@lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.comparable-rate')</label>
                            <div class="input-group">
                                <span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.solar-panels.indication-for-costs.year')</span>
                                <input type="text" id="interest_comparable" class="form-control disabled" disabled="" value="0,0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row system-performance">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert show" role="alert">
                        <p id="performance-text"></p>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function() {

            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $("select, input[type=radio], input[type=text]").change(formChange);

            function formChange(){
                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.solar-panels.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function(data){
                        if (data.hasOwnProperty('advice')){
                            $("#solar-panels-advice").html("<strong>" + data.advice + "</strong>");
                            $(".advice").show();
                        }
                        else {
                            $("#solar-panels-advice").html("");
                            $(".advice").hide();
                        }

                        if (data.hasOwnProperty('yield_electricity')){
                            $("input#yield_electricity").val(Math.round(data.yield_electricity));
                        }
                        if (data.hasOwnProperty('raise_own_consumption')){
                            $("input#raise_own_consumption").val(Math.round(data.raise_own_consumption));
                        }
                        if (data.hasOwnProperty('savings_co2')){
                            $("input#savings_co2").val(Math.round(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')){
                            $("input#savings_money").val(Math.round(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')){
                            $("input#cost_indication").val(Math.round(data.cost_indication));
                        }
                        if (data.hasOwnProperty('interest_comparable')){
                            $("input#interest_comparable").val(data.interest_comparable);
                        }
                        if (data.hasOwnProperty('performance')){
                            $("#performance-text").html("<strong>" + data.performance.text + "</strong>");
                            $(".system-performance .alert").removeClass("alert-danger");
                            $(".system-performance .alert").removeClass("alert-warning");
                            $(".system-performance .alert").removeClass("alert-info");
                            $(".system-performance .alert").addClass("alert-" + data.performance.alert);
                            $(".system-performance").show();
                        }
                        else {
                            $("#performance-text").html("");
                            $(".system-performance").hide();
                        }
                        if (data.hasOwnProperty('total_power')){
                            $("#solar-panels-total-power").html(data.total_power);
                            $(".total-power").show();
                        }
                        else {
                            $("#solar-panels-total-power").html("");
                            $(".total-power").hide();
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            }

            $('form').find('*').filter(':input:visible:first').trigger('change');

        });
    </script>
@endpush