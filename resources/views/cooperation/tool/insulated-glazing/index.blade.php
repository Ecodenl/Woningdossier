@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.insulated-glazing.title'))


@section('step_content')

    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.insulated-glazing.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="main-glass-questions">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.insulated-glazing.title')</h4>
                </div>
            </div>
            {{--@foreach ($keys as $key)--}}
            @foreach($measureApplications as $measureApplication)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space {{ $errors->has('user_interests.' . $measureApplication->id) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#user_interests_{{ $measureApplication->id }}-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{--@lang('woningdossier.cooperation.tool.insulated-glazing.'.$key.'.title')--}}
                                {{ $measureApplication->measure_name }}
                            </label>

                            <select id="{{ $measureApplication->id }}" class="user-interest form-control" name="user_interests[{{ $measureApplication->id }}]" >
                                @foreach($interests as $interest)
                                    <option @if($interest->id == old('user_interests.' . $measureApplication->id) || (array_key_exists($measureApplication->id, $userInterests) && $interest->id == $userInterests[$measureApplication->id]))  selected="selected" @elseif(Auth::user()->getInterestedType('measure_application', $measureApplication->id) != null && Auth::user()->getInterestedType('measure_application', $measureApplication->id)->interest_id == $interest->id) selected @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                @endforeach
                            </select>

                            <div id="user_interests_{{ $measureApplication->id }}-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('user_interests.' . $measureApplication->id))
                                <span class="help-block">
                                    <strong>{{ $errors->first('user_interests.' . $measureApplication->id) }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="values">
                        <div class=" col-sm-3 ">
                            <div class="form-group add-space {{ $errors->has('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') ? ' has-error' : '' }}">
                                <label class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_insulated_glazings_{{ $measureApplication->id }}-insulating_glazing_id-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    @lang('woningdossier.cooperation.tool.insulated-glazing.current-glass')
                                </label>

                                <select class="form-control" name="building_insulated_glazings[{{ $measureApplication->id }}][insulated_glazing_id]">
                                    @foreach($insulatedGlazings as $insulateGlazing)
                                        <option @if($insulateGlazing->id == old('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->insulated_glazing_id)) selected @endif value="{{ $insulateGlazing->id }}">{{ $insulateGlazing->name }}</option>
                                    @endforeach
                                </select>

                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-insulated_glazing_id-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And i would like to have it to...
                                </div>

                                @if ($errors->has('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group add-space {{ $errors->has('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id') ? ' has-error' : '' }}">
                                <label class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_insulated_glazings_{{ $measureApplication->id }}-building_heating_id-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    @lang('woningdossier.cooperation.tool.insulated-glazing.heated-rooms')
                                </label>

                                <select class="form-control" name="building_insulated_glazings[{{ $measureApplication->id }}][building_heating_id]">

                                    @foreach($heatings as $heating)
                                        <option @if($heating->id == old('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->building_heating_id)) selected="selected" @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                    @endforeach

                                </select>

                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-building_heating_id-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And i would like to have it to...
                                </div>

                                @if ($errors->has('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group add-space {{ $errors->has('building_insulated_glazings.' . $measureApplication->id . '.m2') ? ' has-error' : '' }}">
                                <label class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_insulated_glazings_{{ $measureApplication->id }}-m2-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    @lang('woningdossier.cooperation.tool.insulated-glazing.m2')
                                </label>

                                <input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][m2]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.m2', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->m2 : '') }}" class="form-control">

                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-m2-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And i would like to have it to...
                                </div>

                                @if ($errors->has('building_insulated_glazings.' . $measureApplication->id . '.m2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('building_insulated_glazings.' . $measureApplication->id . '.m2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group add-space {{ $errors->has('building_insulated_glazings.' . $measureApplication->id . '.windows') ? ' has-error' : '' }}">
                                <label class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_insulated_glazings_{{ $measureApplication->id }}-windows-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    @lang('woningdossier.cooperation.tool.insulated-glazing.total-windows')
                                </label>

                                <input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][windows]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.windows', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->windows : '') }}"
                                       class="form-control">
                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-windows-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And i would like to have it to...
                                </div>

                                @if ($errors->has('building_insulated_glazings.' . $measureApplication->id . '.windows'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('building_insulated_glazings.' . $measureApplication->id . '.windows') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            @endforeach

        </div>

        <div id="remaining-questions">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('building_elements.'.$crackSealing->id.'.crack-sealing') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building_elements.crack-sealing-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.moving-parts-quality')
                        </label>

                        <select class="form-control" name="building_elements[{{$crackSealing->id}}][crack-sealing]">
                            @foreach($crackSealing->values()->orderBy('order')->get() as $sealingValue)
                                <option @if($sealingValue->id == old('building_elements.crack-sealing') || ($building->getBuildingElement('crack-sealing') instanceof \App\Models\BuildingElement && $building->getBuildingElement('crack-sealing')->element_value_id == $sealingValue->id)) selected @endif value="{{ $sealingValue->id }}">{{ $sealingValue->value }}</option>
                            @endforeach
                        </select>

                        <div id="building_elements.crack-sealing-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('building_elements.crack-sealing'))
                            <span class="help-block">
                                <strong>{{ $errors->first('building_elements.crack-sealing') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('window_surface') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#window-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.windows-surface')
                        </label>

                        <input type="text" name="window_surface"  value="{{ old('window_surface') || isset($building->buildingFeatures->window_surface) ? $building->buildingFeatures->window_surface : '' }}" class="form-control">

                        <div id="window-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('window_surface'))
                            <span class="help-block">
                                <strong>{{ $errors->first('window_surface') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="paint-work">
            <div class="row">
                <div class="col-sm-12">
                    <hr>
                    <h4 style="margin-left: -5px;" >@lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.title')</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('building_elements.'.$frames->id.'.frames') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#which-frames-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.which-frames')
                        </label>

                        <select class="form-control" name="building_elements[{{$frames->id}}][frames]">
                            @foreach($frames->values()->orderBy('order')->get() as $frameValue)
                                <option @if($frameValue->id == old('building_elements.frames')  || ($building->getBuildingElement('frames') instanceof \App\Models\BuildingElement && $building->getBuildingElement('frames')->element_value_id == $frameValue->id)) selected @endif value="{{ $frameValue->id }}">{{ $frameValue->value }}</option>
                            @endforeach
                        </select>

                        <div id="which-frames-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('building_elements.frames'))
                            <span class="help-block">
                            <strong>{{ $errors->first('building_elements.frames') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('building_elements.'.$woodElements->id.'.wood-elements') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#wood-elements-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.other-wood-elements')
                        </label>

                        <div id="wood-element-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('building_elements.wood-elements'))
                            <span class="help-block">
                                <strong>{{ $errors->first('building_elements.wood-elements') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group add-space">
                        @foreach($woodElements->values()->orderBy('order')->get() as $woodElement)
                            <label for="building_elements.wood-elements.{{ $woodElement->id }}" class="checkbox-inline">
                                <input

                                        @if(old('building_elements.wood-elements.'.$woodElements->id.''.$woodElement->id.''))
                                            checked
                                        @elseif($building->buildingElements()->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id)->first() != null && $building->buildingElements()->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id)->first()->element_value_id == $woodElement->id)
                                            checked
                                        @endif
                                        type="checkbox" id="building_elements.wood-elements.{{ $woodElement->id }}" value="{{$woodElement->id}}" name="building_elements[wood-elements][{{ $woodElements->id }}][{{$woodElement->id}}]">
                                {{ $woodElement->value }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('building_paintwork_statuses.last_painted_year') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building_paintwork_statuses.last_painted_year-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.last-paintjob')
                        </label> <span>*</span>

                        <input required type="text" name="building_paintwork_statuses[last_painted_year]" class="form-control" value="{{ old('building_paintwork_statuses.last_painted_year', $building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus ? $building->currentPaintworkStatus->last_painted_year : '') }}">

                        <div id="building_paintwork_statuses.last_painted_year" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('building_paintwork_statuses.last_painted_year'))
                            <span class="help-block">
                            <strong>{{ $errors->first('building_paintwork_statuses.last_painted_year') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('building_paintwork_statuses.paintwork_status_id') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building_paintwork_statuses.paintwork_status_id-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.paint-damage-visible')
                        </label>

                        <select class="form-control" name="building_paintwork_statuses[paintwork_status_id]">
                            @foreach($paintworkStatuses as $paintworkStatus)
                                <option @if($paintworkStatus->id == old('building_paintwork_statuses.paintwork_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->paintwork_status_id == $paintworkStatus->id) ) selected @endif value="{{ $paintworkStatus->id }}">{{ $paintworkStatus->name }}</option>
                            @endforeach
                        </select>

                        <div id="building_paintwork_statuses.paintwork_status_id-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('building_paintwork_statuses.paintwork_status_id'))
                            <span class="help-block">
                            <strong>{{ $errors->first('building_paintwork_statuses.paintwork_status_id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('building_paintwork_statuses.wood_rot_status_id') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building_paintwork_statuses.wood_rot_status_id-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.wood-rot-visible')
                        </label>

                        <select class="form-control" name="building_paintwork_statuses[wood_rot_status_id]">
                            @foreach($woodRotStatuses as $woodRotStatus)
                                <option @if($woodRotStatus->id == old('building_paintwork_statuses.wood_rot_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->wood_rot_status_id == $woodRotStatus->id) ) selected @endif value="{{ $woodRotStatus->id }}">{{ $woodRotStatus->name }}</option>
                            @endforeach
                        </select>

                        <div id="building_paintwork_statuses.wood_rot_status_id-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('building_paintwork_statuses.wood_rot_status_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('building_paintwork_statuses.wood_rot_status_id') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="indication-for-costs">
            <hr>
            <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.title')</h4>

            <div id="costs" class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.gas-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">m3 / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.co2-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">CO2 / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="savings_co2" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.savings-in-euro')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i> / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="savings_money" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.indicative-costs')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="cost_indication" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>

                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.comparable-rate')</label>
                        <div class="input-group">
                            <span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="interest_comparable" class="form-control disabled" disabled="" value="0,0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="taking-into-account">
            <hr>
            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.title')</h4>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.insulated-glazing.taking-into-account.paintwork')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="paintwork_costs" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.insulated-glazing.taking-into-account.paintwork_year')</label>
                        <div class="input-group">
                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="paintwork_year" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
            </div>


        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{ route('cooperation.tool.insulated-glazing.store', [ 'cooperation' => $cooperation ]) }}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="disabled btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function () {
                /*if ($('#is-painted').is(':checked')) {
                    $('#painted-options').show();
                } else {
                    $('#painted-options').hide();
                }*/

                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.insulated-glazing.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {

                        /*
                        if (data.insulation_advice){
                            $("#insulation-advice").html("<strong>" + data.insulation_advice + "</strong>");
                        }
                        */
                        if (data.savings_gas){
                            $("input#savings_gas").val(Math.round(data.savings_gas));
                        }
                        if (data.savings_co2){
                            $("input#savings_co2").val(Math.round(data.savings_co2));
                        }
                        if (data.savings_money){
                            $("input#savings_money").val(Math.round(data.savings_money));
                        }
                        if (data.cost_indication){
                            $("input#cost_indication").val(Math.round(data.cost_indication));
                        }
                        if (data.interest_comparable){
                            $("input#interest_comparable").val(data.interest_comparable);
                        }
                        if (data.paintwork.costs){
                            $("input#paintwork_costs").val(Math.round(data.paintwork.costs));
                        }
                        if (data.paintwork.year){
                            $("input#paintwork_year").val(data.paintwork.year);
                        }
                        console.log(data);
                    }
                });
            });
            // Trigger the change event so it will load the data
            $("select, input[type=radio], input[type=text]").trigger('change');

            $('.user-interest').change(function() {
                $('.user-interest option:selected').each(function() {
                    $userInterest = $(this); // the input field
                    if ($userInterest.text() == "Geen actie" || $userInterest.text() == "Niet mogelijk") {
                        $userInterest.parent().parent().parent().next().hide();
                    } else {
                        $userInterest.parent().parent().parent().next().show();
                    }
                });
            });

            $('.user-interest').trigger('change')
        });

    </script>
@endpush