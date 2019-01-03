@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.heater.title'))


@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.heater.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        @include('cooperation.tool.includes.interested', ['type' => 'service'])
        <div id="heater">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('heater.title.title')}}</h4>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('user_energy_habits.water_comfort_id') ? ' has-error' : '' }}">
                        <label for="user_energy_habits_water_comfort_id" class=" control-label">
                            <i data-toggle="collapse" data-target="#water-comfort-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('heater.comfort-level-warm-tap-water.title')}}
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $comfortLevels, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'water_comfort_id'])
                            <select id="user_energy_habits_water_comfort_id" class="form-control" name="user_energy_habits[water_comfort_id]">
                                @foreach($comfortLevels as $comfortLevel)
                                    <option @if(old('user_energy_habits.water_comfort_id', \App\Helpers\Hoomdossier::getMostCredibleValue(Auth::user()->energyHabit(), 'water_comfort_id')) == $comfortLevel->id) selected @endif value="{{ $comfortLevel->id }}">{{ $comfortLevel->name }}</option>
                                    {{--<option @if(old('user_energy_habits.water_comfort_id') == $comfortLevel->id || ($habits instanceof \App\Models\UserEnergyHabit && $habits->water_comfort_id == $comfortLevel->id)) selected @endif value="{{ $comfortLevel->id }}">{{ $comfortLevel->name }}</option>--}}
                                @endforeach
                            </select>
                        @endcomponent

                        <div id="water-comfort-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('heater.comfort-level-warm-tap-water.help')}}
                        </div>
                        @if ($errors->has('user_energy_habits.water_comfort_id'))
                            <span class="help-block">
                            <strong>{{ $errors->first('user_energy_habits.water_comfort_id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('building_heaters.pv_panel_orientation_id') ? ' has-error' : '' }}">
                        <label for="building_heaters_pv_panel_orientation_id" class=" control-label">
                            <i data-toggle="collapse" data-target="#orientation-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('heater.pv-panel-orientation-id.title')}}
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $collectorOrientations, 'userInputValues' => $currentHeatersForMe, 'userInputColumn' => 'pv_panel_orientation_id'])
                            <select id="building_heaters_pv_panel_orientation_id" class="form-control" name="building_heaters[pv_panel_orientation_id]">
                                @foreach($collectorOrientations as $collectorOrientation)
                                    <option @if(old('building_heaters.pv_panel_orientation_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->heater(), 'pv_panel_orientation_id')) == $collectorOrientation->id) selected="selected" @endif value="{{ $collectorOrientation->id }}">{{ $collectorOrientation->name }}</option>
                                    {{--<option @if(old('building_heaters.pv_panel_orientation_id') == $collectorOrientation->id || ($currentHeater instanceof \App\Models\BuildingHeater && $currentHeater->pv_panel_orientation_id == $collectorOrientation->id)) selected @endif value="{{ $collectorOrientation->id }}">{{ $collectorOrientation->name }}</option>--}}
                                @endforeach
                            </select>
                        @endcomponent

                        <div id="orientation-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('heater.pv-panel-orientation-id.help')}}
                        </div>

                        @if ($errors->has('building_heaters.pv_panel_orientation_id'))
                            <span class="help-block">
                            <strong>{{ $errors->first('building_heaters.pv_panel_orientation_id') }}</strong>
                        </span>
                        @endif
                    </div>

                </div>

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('building_heaters.angle') ? ' has-error' : '' }}">
                        <label for="building_heaters_angle" class=" control-label">
                            <i data-toggle="collapse" data-target="#angle-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('heater.angle.title')}}
                        </label>


                        <?php $angles = [20, 30, 40, 45, 50, 60, 70, 75, 90]  ?>
                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => array_combine($angles, $angles), 'userInputValues' => $currentHeatersForMe, 'userInputColumn' => 'angle'])
                            <span class="input-group-addon">&deg;</span>
                            <select id="building_heaters_angle" class="form-control" name="building_heaters[angle]">
                                @foreach($angles as $angle)
                                    <option @if(old('building_heaters.angle', \App\Helpers\Hoomdossier::getMostCredibleValue($building->heater(), 'angle')) == $angle) selected @endif value="{{ $angle }}">{{ $angle }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                        <div id="angle-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('heater.angle.help')}}
                        </div>

                        @if ($errors->has('building_heaters.angle'))
                            <span class="help-block">
                            <strong>{{ $errors->first('building_heaters.angle') }}</strong>
                        </span>
                        @endif
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('comment') ? ' has-error' : '' }}">
                        <label for="additional-info" class=" control-label"><i data-toggle="collapse" data-target="#additional-info-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>{{\App\Helpers\Translation::translate('general.specific-situation.title')}}</label>

                        <textarea id="additional-info" class="form-control" name="comment">{{old('comment', isset($currentHeater) ? $currentHeater->comment : '')}}</textarea>

                        <div id="additional-info-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('general.specific-situation.help')}}
                        </div>

                        @if ($errors->has('comment'))
                            <span class="help-block">
                                <strong>{{ $errors->first('comment') }}</strong>
                            </span>
                        @endif

                    </div>
                </div>
            </div>
            @if(\App\Models\BuildingService::hasCoachInputSource($currentHeatersForMe) && Auth::user()->hasRole('resident'))
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space">
                            <?php
                            $coachInputSource = \App\Models\BuildingService::getCoachInput($currentHeatersForMe);
                            $comment = $coachInputSource->comment;
                            ?>
                            <label for="" class=" control-label"><i data-toggle="collapse" data-target="#comment" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('default.form.input.comment') ({{$coachInputSource->getInputSourceName()}})
                            </label>
                            <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                        </div>
                    </div>
                </div>
            @elseif(\App\Models\BuildingService::hasResidentInputSource($currentHeatersForMe) && Auth::user()->hasRole('coach'))
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space">
                            <?php
                            $residentInputSource = \App\Models\BuildingService::getResidentInput($currentHeatersForMe);
                            $comment = $residentInputSource->comment;
                            ?>
                            <label for="" class=" control-label"><i data-toggle="collapse" data-target="#comment" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('default.form.input.comment') ({{$residentInputSource->getInputSourceName()}})
                            </label>

                            <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                        </div>
                    </div>
                </div>
            @endif

            <div id="estimated-usage">
                <hr>
                <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('heater.estimated-usage.title.title')}}</h4>

                <div id="consumption" class="row">

                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">{{\App\Helpers\Translation::translate('heater.consumption-water.title')}}</label>
                            <div class="input-group">
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.liter.title') }} / {{\App\Helpers\Translation::translate('general.unit.day.title')}}</span>
                                <input type="text" id="consumption_water" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">{{\App\Helpers\Translation::translate('heater.consumption-gas.title')}}</label>
                            <div class="input-group">
                                <span class="input-group-addon">m<sup>3</sup> / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                                <input type="text" id="consumption_gas" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="system-specs">
                <hr>
                <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('heater.system-specs.title')}}</h4>

                <div id="consumption" class="row">

                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">{{\App\Helpers\Translation::translate('heater.size-boiler.title')}}</label>
                            <div class="input-group">
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.liter.title')}}</span>
                                <input type="text" id="size_boiler" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">{{\App\Helpers\Translation::translate('heater.size-collector.title')}}</label>
                            <div class="input-group">
                                <span class="input-group-addon">m<sup>2</sup></span>
                                <input type="text" id="size_collector" class="form-control disabled" disabled="" value="0">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="indication-for-costs">
                <hr>
                <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('heater.indication-for-costs.title.title')}}</h4>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">
                                <i data-toggle="collapse" data-target="#production-heat-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('heater.indication-for-costs.production-heat.title')}}
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">kWh / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                                <input type="text" id="production_heat" class="form-control disabled" disabled="" value="0">
                            </div>
                            <div id="production-heat-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('heater.indication-for-costs.production-heat.help')}}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">
                                <i data-toggle="collapse" data-target="#percentage-consumption-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('heater.indication-for-costs.percentage-consumption.title')}}
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input type="text" id="percentage_consumption" class="form-control disabled" disabled="" value="0">
                            </div>
                            <div id="percentage-consumption-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('heater.indication-for-costs.percentage-consumption.title')}}
                            </div>
                        </div>
                    </div>

                </div>

                </div>
                <div class="row">
                    <div class="col-sm-4">
                        @include('cooperation.layouts.indication-for-costs.gas', ['step' => $currentStep->slug])
                    </div>
                    <div class="col-sm-4">
                        @include('cooperation.layouts.indication-for-costs.co2', ['step' => $currentStep->slug])
                    </div>
                    <div class="col-sm-4">
                        @include('cooperation.layouts.indication-for-costs.savings-in-euro')
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        @include('cooperation.layouts.indication-for-costs.indicative-costs')
                    </div>
                    <div class="col-sm-6">
                        @include('cooperation.layouts.indication-for-costs.comparable-rent')
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

            <div class="row">
                <div class="col-md-12">
                    {{--<div class="panel panel-primary">--}}
                        {{--<div class="panel-heading">@lang('default.buttons.download')</div>--}}
                        {{--<div class="panel-body">--}}
                            {{--<ol>--}}
                                {{--<li><a download="" href="{{asset('storage/hoomdossier-assets/maatregelblad_warmtepomp.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/maatregelblad_warmtepomp.pdf')))))}}</a></li>--}}
                            {{--</ol>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="panel panel-primary">
                        <div class="panel-heading">@lang('default.buttons.download')</div>
                        <div class="panel-body">
                            <ol>
                                <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Zonneboiler.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Zonneboiler.pdf')))))}}</a></li>
                            </ol>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group add-space">
                        <div class="">
                            <a class="btn btn-success pull-left"  href="{{route('cooperation.tool.solar-panels.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                            <button type="submit"  class="btn btn-primary pull-right">
                                @lang('default.buttons.next')
                            </button>
                        </div>
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
                    url: '{{ route('cooperation.tool.heater.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function(data){
                        if (data.hasOwnProperty('consumption')){
                            $("input#consumption_water").val(Math.round(data.consumption.water));
                            $("input#consumption_gas").val(Math.round(data.consumption.gas));
                        }
                        if (data.hasOwnProperty('specs')){
                            $("input#size_boiler").val(Math.round(data.specs.size_boiler));
                            $("input#size_collector").val(data.specs.size_collector);
                        }
                        if (data.hasOwnProperty('production_heat')){
                            $("input#production_heat").val(Math.round(data.production_heat));
                        }
                        if (data.hasOwnProperty('percentage_consumption')){
                            $("input#percentage_consumption").val(Math.round(data.percentage_consumption));
                        }
                        if (data.hasOwnProperty('savings_gas')){
                            $("input#savings_gas").val(Math.round(data.savings_gas));
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

