@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.roof-insulation.title'))

@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.roof-insulation.store', ['cooperation' => $cooperation]) }}">
        {{csrf_field()}}
        <div class="row">
            <div id="current-situation" class="col-md-12">
                <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.title')</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space {{ $errors->has('building_roof_types') ? ' has-error' : '' }}">
                            <label for="building_roof_types" class="control-label"><i data-toggle="collapse" data-target="#roof-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.roof-types')</label>
                            <br>
                            @foreach($roofTypes as $roofType)
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="building_roof_types[]" value="{{ $roofType->id }}"
                                    @if((is_array(old('building_roof_types')) && in_array($roofType->id, old('building_roof_types'))) || ($currentRoofTypes->contains('roof_type_id', $roofType->id))) checked @endif>
                                    {{ $roofType->name }}
                                </label>
                            @endforeach

                            @if ($errors->has('building_roof_types'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('building_roof_types') }}</strong>
                                </span>
                            @endif

                            <div class="col-sm-12">
                                <div class="form-group add-space">
                                    <div id="roof-type-info" class="collapse alert alert-info remove-collapse-space">
                                        I would like to have some help full information right here!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="if-roof">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group add-space {{$errors->has('building_features.roof_type_id') ? ' has-error' : ''}}">

                                <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#main-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.main-roof')</label>

                                <select id="main_roof" class="form-control" name="building_features[roof_type_id]" >
                                    @foreach($roofTypes as $roofType)
                                        @if($roofType->calculate_value < 5)
                                        <option @if($roofType->id == old('building_features.roof_type_id') || ($features->roof_type_id == $roofType->id)) selected @endif value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                        @endif
                                    @endforeach
                                </select>

                                <div id="main-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>
                                @if ($errors->has('building_features.roof_type_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('building_features.roof_type_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @foreach(['flat', 'pitched'] as $roofCat)

                        <div class="{{ $roofCat }}-roof">

                            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.roof-insulation.' . $roofCat . '-roof.title')</h4>
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.element_value_id') ? ' has-error' : '' }}">

                                        <label for="flat_roof_insulation" class=" control-label"><i data-toggle="collapse" data-target="#{{ $roofCat }}-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.is-' . $roofCat . '-roof-insulated')</label>

                                        <select id="flat_roof_insulation" class="form-control" name="building_roof_types[{{ $roofCat }}][element_value_id]" >
                                            @foreach($roofInsulation->values as $insulation)
                                                @if($insulation->calculate_value < 6)
                                                <option @if($insulation->id == old('building_roof_types.' . $roofCat . '.element_value_id') || (isset($currentCategorizedRoofTypes[$roofCat]['element_value_id']) && $currentCategorizedRoofTypes[$roofCat]['element_value_id'] == $insulation->id)) selected @endif value="{{ $insulation->id }}">{{ $insulation->value }}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                        <div id="{{ $roofCat }}-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            And I would like to have it too...
                                        </div>
                                        @if ($errors->has('building_roof_types.' . $roofCat . '.element_value_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.element_value_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.surface') ? ' has-error' : '' }}">

                                        <label for="flat-roof-surfaces" class=" control-label"><i data-toggle="collapse" data-target="#{{ $roofCat }}-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.' . $roofCat . '-roof-surface')</label> <span> *</span>

                                        <div class="input-group">
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                                            <input  type="number" min="0" class="form-control" name="building_roof_types[{{ $roofCat }}][surface]" value="{{isset($currentCategorizedRoofTypes[$roofCat]['surface']) ? $currentCategorizedRoofTypes[$roofCat]['surface'] : old('building_roof_types.' . $roofCat . '.surface')}}">
                                        </div>

                                        <div id="{{ $roofCat }}-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            And I would like to have it too...
                                        </div>

                                        @if ($errors->has('building_roof_types.' . $roofCat . '.surface'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.surface') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row cover-zinc">
                                <div class="col-md-12">
                                    <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date') ? ' has-error' : '' }}">
                                        <label for="zinc-replaced" class="control-label"><i data-toggle="collapse" data-target="#zinc-replaced-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.zinc-replaced')</label> <span> *</span>

                                        <div class="input-group">
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.year')</span>
                                            <input  type="number" min="1800" class="form-control" name="building_roof_types[{{ $roofCat }}][extra][zinc_replaced_date]" value="{{ isset($currentCategorizedRoofTypes[$roofCat]['extra']['zinc_replaced_date']) ? $currentCategorizedRoofTypes[$roofCat]['extra']['zinc_replaced_date'] : old('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date') }}">
                                        </div>

                                        <div id="zinc-replaced-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            And I would like to have it too...
                                        </div>

                                        @if ($errors->has('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date'))
                                            <span class="help-block">
                                                    <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date') }}</strong>
                                                </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row cover-bitumen">
                                <div class="col-md-12">
                                    <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date') ? ' has-error' : '' }}">
                                        <label for="bitumen-replaced" class=" control-label"><i data-toggle="collapse" data-target="#bitumen-replaced-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.bitumen-insulated')</label> <span> *</span>

				                        <?php
				                        $default = (isset($currentCategorizedRoofTypes[$roofCat]['extra']['bitumen_replaced_date']) && $currentCategorizedRoofTypes[$roofCat]['extra']['bitumen_replaced_date'] != 1) ? $currentCategorizedRoofTypes[$roofCat]['extra']['bitumen_replaced_date'] : '';
				                        ?>

                                        <div class="input-group">
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.year')</span>
                                            <input  type="number" class="form-control" name="building_roof_types[{{ $roofCat }}][extra][bitumen_replaced_date]"
                                                value="{{ old('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date', $default) }}">
                                        </div>

                                        <div id="bitumen-replaced-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            And I would like to have it too...
                                        </div>

                                        @if ($errors->has('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date'))
                                            <span class="help-block">
                                                    <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date') }}</strong>
                                                </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($roofCat == 'pitched')

                                <div class="row cover-tiles">
                                    <div class="col-md-12">
                                        <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.extra.tiles_condition') ? ' has-error' : '' }}">

                                            <label for="tiles_condition" class=" control-label"><i data-toggle="collapse" data-target="#tiles-condition-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.in-which-condition-tiles')</label>

                                            <?php
                                                $default = (isset($currentCategorizedRoofTypes[$roofCat]['extra']['tiles_condition']) && $currentCategorizedRoofTypes[$roofCat]['extra']['tiles_condition'] != 1) ? $currentCategorizedRoofTypes[$roofCat]['extra']['tiles_condition'] : '';
                                            ?>

                                            <select  id="tiles_condition" class="form-control" name="building_roof_types[{{ $roofCat }}][extra][tiles_condition]" >
                                                @foreach($roofTileStatuses as $roofTileStatus)
                                                    <option @if($roofTileStatus->id == old('building_roof_types.' . $roofCat . '.extra.tiles_condition', $default)) selected  @endif value="{{ $roofTileStatus->id }}">{{ $roofTileStatus->name }}</option>
                                                @endforeach
                                            </select>

                                            <div id="tiles-condition-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                And I would like to have it too...
                                            </div>

                                            @if ($errors->has('building_roof_types.' . $roofCat . '.extra.tiles_condition'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.extra.tiles_condition') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group add-space {{$errors->has('building_roof_types.' . $roofCat . '.extra.measure_application_id') ? ' has-error' : ''}}">

                                        <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#{{ $roofCat }}-interested-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.' . $roofCat . '-roof.insulate-roof')</label>

                                        <?php
                                            $default = isset($currentCategorizedRoofTypes[$roofCat]['extra']['measure_application_id']) ? $currentCategorizedRoofTypes[$roofCat]['extra']['measure_application_id'] : 0;
                                        ?>

                                        <select id="flat_roof_insulation" class="form-control" name="building_roof_types[{{ $roofCat }}][measure_application_id]">
                                                <option value="0" @if($default == 0) selected @endif>@lang('woningdossier.cooperation.tool.roof-insulation.measure-application.no')</option>
                                            @foreach($measureApplications[$roofCat] as $measureApplication)
                                                <option @if($measureApplication->id == old('building_roof_types.' . $roofCat . '.extra.measure_application_id', $default)) selected @endif value="{{ $measureApplication->id }}">{{ $measureApplication->measure_name }}</option>
                                            @endforeach
                                        </select>

                                        <div id="{{ $roofCat }}-interested-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            And I would like to have it too...
                                        </div>
                                        @if ($errors->has('building_roof_types.' . $roofCat . '.extra.measure_application_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.extra.measure_application_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group add-space {{$errors->has('building_roof_types.' . $roofCat . '.building_heating_id') ? ' has-error' : ''}}">

                                        <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#{{ $roofCat }}-heating-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.' . $roofCat . '-roof.situation')</label>

                                        <?php
	                                    $default = isset($currentCategorizedRoofTypes[$roofCat]['building_heating_id']) ? $currentCategorizedRoofTypes[$roofCat]['building_heating_id'] : 0;
                                        ?>

                                        <select id="flat_roof_situation" class="form-control" name="building_roof_types[{{ $roofCat }}][building_heating_id]" >
                                            @foreach($heatings as $heating)
                                                @if($heating->calculate_value < 5)
                                                <option @if($heating->id == old('building_roof_types.' . $roofCat . '.building_heating_id', $default)) selected @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                        <div id="{{ $roofCat }}-heating-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            And I would like to have it too...
                                        </div>
                                        @if ($errors->has('building_roof_types.' . $roofCat . '.building_heating_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.building_heating_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group add-space {{ $errors->has('comments') ? ' has-error' : '' }}">
                                        <label for="" class="control-label">
                                            <i data-toggle="collapse" data-target="#comments-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                            @lang('default.form.input.comment')
                                        </label>

	                                    <?php
	                                    $default = isset($currentCategorizedRoofTypes[$roofCat]['extra']['comment']) ? $currentCategorizedRoofTypes[$roofCat]['building_heating_id']['comment'] : "";
	                                    ?>

                                        <textarea name="building_roof_types[{{ $roofCat }}][extra][comment]" id="" class="form-control">{{ $default }}</textarea>

                                        <div id="comments-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            And i would like to have it to...
                                        </div>

                                        @if ($errors->has('comments'))
                                            <span class="help-block">
                                <strong>{{ $errors->first('comments') }}</strong>
                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                    @endforeach

                    @foreach(['flat', 'pitched'] as $roofCat)
                        <div class="costs {{ $roofCat }}-roof">
                            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.roof-insulation.costs.' . $roofCat . '.title')</h4>
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group add-space">
                                        <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.gas')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.cubic-meters') / @lang('woningdossier.cooperation.tool.unit.year')</span>
                                            <input type="text" id="{{ $roofCat }}_savings_gas" class="form-control disabled" disabled="" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group add-space">
                                        <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.co2')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.kilograms') / @lang('woningdossier.cooperation.tool.unit.year')</span>
                                            <input type="text" id="{{ $roofCat }}_savings_co2" class="form-control disabled" disabled="" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group add-space">
                                        <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.savings-in-euro')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                            <input type="text" id="{{ $roofCat }}_savings_money" class="form-control disabled" disabled="" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group add-space">
                                        <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.indicative-costs-insulation')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                            <input type="text" id="{{ $roofCat }}_cost_indication" class="form-control disabled" disabled="" value="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group add-space @if($roofCat == 'pitched') cover-tiles @endif">
                                        <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.' . $roofCat . '.indicative-costs-replacement')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                            <input type="text" id="{{ $roofCat }}_replace_cost" class="form-control disabled" disabled="" value="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group add-space @if($roofCat == 'pitched') cover-tiles @endif">
                                        <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.' . $roofCat . '.indicative-replacement-year')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                            <input type="text" id="{{ $roofCat }}_replace_year" class="form-control disabled" disabled="" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group add-space">
                                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.comparable-rate')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                                            <input type="text" id="{{ $roofCat }}_interest_comparable" class="form-control disabled" disabled="" value="0,0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Dakisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Dakisolatie.pdf')))))}}</a></li>
                        </ol>
                    </div>
                </div>
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{route('cooperation.tool.floor-insulation.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" class=" btn btn-primary pull-right">
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

            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $("select, input[type=radio], input[type=text], input[type=number], input[type=checkbox]").change(formChange);

            function formChange(){
                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.roof-insulation.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function(data){
                        if (!data.hasOwnProperty('flat') && !data.hasOwnProperty('pitched')){
                            $(".if-roof").hide();
                        }
                        else {
                            $(".if-roof").show();
                        }

                        // default
                        //$(".cover-zinc").hide();
                        $(".flat-roof .cover-bitumen").hide();
                        $(".pitched-roof .cover-bitumen").hide();


                        if (data.hasOwnProperty('flat')){
                            $(".flat-roof").show();
                            $(".flat-roof .cover-bitumen").show();
                            //if (data.flat.hasOwnProperty('type') && data.flat.type === 'zinc'){
                            //    $(".cover-zinc").show();
                            //}
                            if (data.flat.hasOwnProperty('savings_gas')){
                                $("input#flat_savings_gas").val(Math.round(data.flat.savings_gas));
                            }
                            if (data.flat.hasOwnProperty('savings_co2')){
                                $("input#flat_savings_co2").val(Math.round(data.flat.savings_co2));
                            }
                            if (data.flat.hasOwnProperty('savings_money')){
                                $("input#flat_savings_money").val(Math.round(data.flat.savings_money));
                            }
                            if (data.flat.hasOwnProperty('cost_indication')){
                                $("input#flat_cost_indication").val(Math.round(data.flat.cost_indication));
                            }
                            if (data.flat.hasOwnProperty('interest_comparable')){
                                $("input#flat_interest_comparable").val(data.flat.interest_comparable);
                            }
                            if (data.flat.hasOwnProperty('replace')){
                                if (data.flat.replace.hasOwnProperty('year')){
                                    $("input#flat_replace_year").val(data.flat.replace.year);
                                }
                                if (data.flat.replace.hasOwnProperty('cost')){
                                    $("input#flat_replace_cost").val(Math.round(data.flat.replace.cost));
                                }
                            }
                        }
                        else {
                            $(".flat-roof").hide();
                        }

                        $(".cover-tiles").hide();
                        if (data.hasOwnProperty('pitched')){
                            $(".pitched-roof").show();
                            if (data.pitched.hasOwnProperty('type')){
                                if(data.pitched.type === 'tiles'){
                                    $(".cover-tiles").show();
                                    $(".pitched-roof .cover-bitumen").hide();
                                }
                                if (data.pitched.type === 'bitumen'){
                                    $(".pitched-roof .cover-bitumen").show();
                                }
                            }
                            if (data.pitched.hasOwnProperty('savings_gas')){
                                $("input#pitched_savings_gas").val(Math.round(data.pitched.savings_gas));
                            }
                            if (data.pitched.hasOwnProperty('savings_co2')){
                                $("input#pitched_savings_co2").val(Math.round(data.pitched.savings_co2));
                            }
                            if (data.pitched.hasOwnProperty('savings_money')){
                                $("input#pitched_savings_money").val(Math.round(data.pitched.savings_money));
                            }
                            if (data.pitched.hasOwnProperty('cost_indication')){
                                $("input#pitched_cost_indication").val(Math.round(data.pitched.cost_indication));
                            }
                            if (data.pitched.hasOwnProperty('interest_comparable')){
                                $("input#pitched_interest_comparable").val(data.pitched.interest_comparable);
                            }
                            if (data.pitched.hasOwnProperty('replace')){
                                if (data.pitched.replace.hasOwnProperty('year')){
                                    $("input#pitched_replace_year").val(data.pitched.replace.year);
                                }
                                if (data.pitched.replace.hasOwnProperty('cost')){
                                    $("input#pitched_replace_cost").val(Math.round(data.pitched.replace.cost));
                                }
                            }
                        }
                        else {
                            $(".pitched-roof").hide();
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