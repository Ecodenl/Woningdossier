@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.roof-insulation.title'))

@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.general-data.store', ['cooperation' => $cooperation]) }}">

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
                                    @if(in_array($roofType->id, old('building_roof_types')) || ($features instanceof \App\Models\BuildingFeature && $features->roof_type_id == $roofType->id) || ($currentRoofTypes instanceof \Illuminate\Support\Collection && $currentRoofTypes->contains('id', $roofType->id))) selected @endif>
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
                        <div class="col-md-6">
                            <div class="form-group add-space {{$errors->has('building_features.roof_type_id') ? ' has-error' : ''}}">

                                <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#main-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.main-roof')</label>

                                <select id="main_roof" class="form-control" name="building_features[roof_type_id]" >
                                    @foreach($roofTypes as $roofType)
                                        <option @if($roofType->id == old('building_features.roof_type_id') || ($features instanceof \App\Models\BuildingFeature && $features->roof_type_id == $roofType->id)) selected @endif value="{{ $roofType->id }}">{{ $roofType->name }}</option>
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
                        <div class="col-md-6">
                            <div class="form-group add-space {{$errors->has('flat_roof_insulation') ? ' has-error' : ''}}">

                                <label for="flat_roof_insulation" class=" control-label"><i data-toggle="collapse" data-target="#flat-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.is-flat-roof-insulated')</label>

                                <select id="flat_roof_insulation" class="form-control" name="flat_roof_insulation" >
                                    @foreach($roofTypes as $roofType)
                                        <option @if($roofType->id == old('flat_roof_insulation')) selected @endif value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                    @endforeach
                                </select>

                                <div id="flat-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>
                                @if ($errors->has('flat_roof_insulation'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('flat_roof_insulation') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group add-space {{$errors->has('bitumen_insulated') ? ' has-error' : ''}}">

                                <label for="bitumen_insulated" class=" control-label"><i data-toggle="collapse" data-target="#bitumen-insulated-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.bitumen-insulated')</label>

                                <input type="number" class="form-control" value="@if(old('bitumen_insulated')) {{old('bitumen_insulated')}} @elseif(isset($answer)) {{$answer}} @endif">

                                <div id="bitumen-insulated-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>

                                @if ($errors->has('bitumen_insulated'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('bitumen_insulated') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group add-space {{$errors->has('flat-roof-surfaces') ? ' has-error' : ''}}">

                                <label for="flat-roof-surfaces" class=" control-label"><i data-toggle="collapse" data-target="#comparable-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.flat-roof-surface.comparable-houses')</label>
                                <br>
                                <label for="flat-roof-surfaces" class=" control-label">@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.flat-roof-surface.not-right')</label>

                                <input type="number" class="form-control" value="@if(old('flat-roof-surfaces')) {{old('flat-roof-surfaces')}} @elseif(isset($answer)) {{$answer}} @endif">

                                <div id="comparable-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>

                                @if ($errors->has('flat-roof-surfaces'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('flat-roof-surfaces') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group add-space {{$errors->has('pitched_roof_insulation') ? ' has-error' : ''}}">

                                <label for="pitched_roof_insulation" class=" control-label"><i data-toggle="collapse" data-target="#pitched-roof-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.pitched-roof')</label>

                                <select id="pitched_roof_insulation" class="form-control" name="pitched_roof_insulation" >
                                    @foreach($qualities as $quality)
                                        <option @if($quality->id == old('pitched_roof_insulation')) selected @elseif(isset($answer)) selected  @endif value="{{ $quality->id }}">{{ $quality->name }}</option>
                                    @endforeach
                                </select>

                                <div id="pitched-roof-insulation-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>

                                @if ($errors->has('pitched_roof_insulation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pitched_roof_insulation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group add-space {{$errors->has('tiles_condition') ? ' has-error' : ''}}">

                                <label for="tiles_condition" class=" control-label"><i data-toggle="collapse" data-target="#tiles-condition-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.in-which-condition-tiles')</label>

                                <select id="tiles_condition" class="form-control" name="tiles_condition" >
                                    @foreach($qualities as $quality)
                                        <option @if($quality->id == old('tiles_condition')) selected @elseif(isset($answer)) selected  @endif value="{{ $quality->id }}">{{ $quality->name }}</option>
                                    @endforeach
                                </select>

                                <div id="tiles-condition-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>

                                @if ($errors->has('tiles_condition'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tiles_condition') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group add-space {{$errors->has('zinc-replaced') ? ' has-error' : ''}}">
                                <label for="zinc-replaced" class=" control-label"><i data-toggle="collapse" data-target="#zinc-replaced-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.zinc-replaced')</label>

                                <input type="date" class="form-control" value="@if(old('zinc-replaced')) {{old('zinc-replaced')}} @elseif(isset($answer)) {{$answer}} @endif">

                                <div id="zinc-replaced-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>

                                @if ($errors->has('zinc-replaced'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('zinc-replaced') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group add-space {{$errors->has('pitched-roof-surfaces') ? ' has-error' : ''}}">

                                <label for="pitched-roof-surfaces" class=" control-label"><i data-toggle="collapse" data-target="#pitched-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.pitched-roof-surface.comparable-houses')</label>
                                <br>
                                <label for="pitched-roof-surfaces" class=" control-label">@lang('woningdossier.cooperation.tool.roof-insulation.current-situation.pitched-roof-surface.not-right')</label>

                                <input type="number" class="form-control" value="@if(old('pitched-roof-surfaces')) {{old('pitched-roof-surfaces')}} @elseif(isset($answer)) {{$answer}} @endif">

                                <div id="pitched-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>

                                @if ($errors->has('pitched-roof-surfaces'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pitched-roof-surfaces') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div id="flat-roof" class="col-md-12">
                    <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.roof-insulation.flat-roof.title')</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group add-space {{$errors->has('flat_roof_insulation') ? ' has-error' : ''}}">

                                <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#main-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.flat-roof.insulate-roof')</label>

                                <select id="flat_roof_insulation" class="form-control" name="flat_roof_insulation" >
                                    @foreach($roofTypes as $roofType)
                                        <option @if($roofType->id == old('flat_roof_insulation')) selected @endif value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                    @endforeach
                                </select>

                                <div id="main-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>
                                @if ($errors->has('flat_roof_insulation'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('flat_roof_insulation') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group add-space {{$errors->has('flat_roof_situation') ? ' has-error' : ''}}">

                                <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#main-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.flat-roof.situation')</label>

                                <select id="flat_roof_situation" class="form-control" name="flat_roof_situation" >
                                    @foreach($heatings as $heating)
                                        <option @if($heating->id == old('flat_roof_situation')) selected @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                    @endforeach
                                </select>

                                <div id="main-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>
                                @if ($errors->has('flat_roof_situation'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('flat_roof_situation') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div id="pitched-roof" class="col-md-12">
                    <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.roof-insulation.pitched-roof.title')</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group add-space {{$errors->has('pitched_roof_insulation') ? ' has-error' : ''}}">

                                <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#main-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.pitched-roof.insulate-roof')</label>

                                <select id="pitched_roof_insulation" class="form-control" name="pitched_roof_insulation" >
                                    @foreach($roofTypes as $roofType)
                                        <option @if($roofType->id == old('pitched_roof_insulation')) selected @endif value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                    @endforeach
                                </select>

                                <div id="main-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>
                                @if ($errors->has('pitched_roof_insulation'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('pitched_roof_insulation') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group add-space {{$errors->has('pitched_roof_situation') ? ' has-error' : ''}}">

                                <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#main-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.roof-insulation.pitched-roof.situation')</label>

                                <select id="pitched_roof_situation" class="form-control" name="pitched_roof_situation" >
                                    @foreach($heatings as $heating)
                                        <option @if($heating->id == old('pitched_roof_situation')) selected @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                    @endforeach
                                </select>

                                <div id="main-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And I would like to have it too...
                                </div>
                                @if ($errors->has('pitched_roof_situation'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('pitched_roof_situation') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
    {{--            @foreach($costs as $cost) (or something)--}}
                <div id="costs" class="col-sm-12">
                    <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.roof-insulation.costs.title')</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group add-space">
                                <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.gas')         </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                    <input type="text" class="form-control disabled" disabled="" value="114">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group add-space">
                                <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.co2')         </label>
                                <div class="input-group">
                                    <span class="input-group-addon">co<sup>2</sup></span>
                                    <input type="text" class="form-control disabled" disabled="" value="114">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group add-space">
                                <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.savings-in-euro')         </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                    <input type="text" class="form-control disabled" disabled="" value="114">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group add-space">
                                <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.indicative-costs-insulation')      </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                    <input type="text" class="form-control disabled" disabled="" value="114">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group add-space">
                                <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.indicative-costs-replacement')      </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                    <input type="datetime-local" class="form-control disabled" disabled="" value="114">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group add-space">
                                <label class="control-label">@lang('woningdossier.cooperation.tool.roof-insulation.costs.indicative-replace-date')      </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                    <input type="text" class="form-control disabled" disabled="" value="114">
                                </div>
                            </div>
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
                        <a class="btn btn-success pull-left" href="{{route('cooperation.tool.floor-insulation.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
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

            formChange();

            $("select, input[type=radio], input[type=text]").change(formChange);

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


                        if (data.insulation_advice){
                            $("#insulation-advice").html("<strong>" + data.insulation_advice + "</strong>");
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
                        if (data.hasOwnProperty('crawlspace_access')){
                            $("#crawlspace-no-access-error").show();
                        }
                        else {
                            $("#crawlspace-no-access-error").hide();
                        }
                        console.log(data);
                    }
                });
            }

        });
    </script>
@endpush