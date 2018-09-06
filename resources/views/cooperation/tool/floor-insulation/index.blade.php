@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.floor-insulation.intro.title'))


@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.floor-insulation.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="floor-insulation">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.floor-insulation.title')</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('element.' . $floorInsulation->id) ? ' has-error' : '' }}">

                        <label for="element_{{ $floorInsulation->id }}" class="control-label">
                            <i data-toggle="collapse" data-target="#floor-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i> @lang('woningdossier.cooperation.tool.floor-insulation.floor-insulation')
                        </label>

                        <div id="floor-insulation-options">
                            <select id="element_{{ $floorInsulation->id }}" class="form-control" name="element[{{ $floorInsulation->id }}]">
                                @foreach($floorInsulation->values()->orderBy('order')->get() as $elementValue)
                                    <option
                                            @if(old('element.' . $floorInsulation->id . '') && $floorInsulation->id == old('element.' . $floorInsulation->element->id . ''))
                                            selected="selected"
                                            @elseif(isset($buildingFeature->element_values) && $elementValue->id == $buildingFeature->element_values)
                                            selected="selected"
                                            @elseif(isset($buildingInsulation->element_value_id) && $elementValue->id == $buildingInsulation->element_value_id)
                                            selected="selected"
                                            @endif
                                            value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if ($errors->has('element.' . $floorInsulation->id))
                            <span class="help-block">
                                <strong>{{ $errors->first('element.' . $floorInsulation->id) }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div class="col-sm-12">
                    <div class="form-group add-space">
                        <div id="house-insulation-info" class="collapse alert alert-info remove-collapse-space">
                            I would like to have some helpful information right here!
                        </div>
                    </div>
                </div>
            </div>

            <div id="answers">

                <div class="row">
                    <div class="col-sm-12">
                        <div id="has-no-crawlspace"
                             class="form-group add-space{{ $errors->has('building_elements.crawlspace') ? ' has-error' : '' }}">
                            <label for="has_crawlspace" class=" control-label">
                                <i data-toggle="collapse" data-target="#building_elements-crawlspace-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.floor-insulation.has-crawlspace.title')
                            </label>

                            <select id="has_crawlspace" class="form-control" name="building_elements[crawlspace]">
                                @foreach(__('woningdossier.cooperation.option') as $i => $option)
                                    <option @if(old('building_elements.crawlspace') == $i) selected
                                            @elseif(isset($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                        && is_array($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                        && array_key_exists('has_crawlspace', $buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                        && $buildingElement->where('element_id', $crawlspace->id)->first()->extra['has_crawlspace'] == $i ) selected
                                            @endif value="{{ $i }}">{{ $option }}</option>
                                @endforeach
                            </select>

                            <div class="col-sm-12">
                                <div class="form-group add-space">
                                    <div id="building_elements-crawlspace-info" class="collapse alert alert-info remove-collapse-space">
                                        I would like to have some help full information right here !
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('building_elements.crawlspace'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('building_elements.crawlspace') }}</strong>
                                </span>
                            @endif
                            
                            

                            <div id="crawlspace-unknown-error" class="help-block" style="display: none;">
                                <div class="alert alert-warning show" role="alert">
                                    <p>@lang('woningdossier.cooperation.tool.floor-insulation.has-crawlspace.unknown')</p>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div id="crawlspace-wrapper" class="crawlspace-accessible">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div id="has-crawlspace-access" class="form-group add-space {{ $errors->has('building_elements.' . $crawlspace->id .'.extra') ? ' has-error' : '' }}">
                                <label for="crawlspace_access" class="control-label">
                                    <i data-toggle="collapse" data-target="#crawlspace-access-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    @lang('woningdossier.cooperation.tool.floor-insulation.crawlspace-access.title')
                                </label>

                            <select id="crawlspace_access" class="form-control" name="building_elements[{{ $crawlspace->id }}][extra]">
                                @foreach(__('woningdossier.cooperation.option') as $i => $option)
                                    <option @if(old('building_elements.crawlspace_access') == $option) selected
                                            @elseif(isset($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                            &&is_array($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                            && array_key_exists('access', $buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                            && $buildingElement->where('element_id', $crawlspace->id)->first()->extra['access'] == $i) selected
                                            @endif value="{{ $i }}">{{ $option }}</option>
                                @endforeach
                            </select>

                                <div class="col-sm-12">
                                    <div class="form-group add-space">
                                        <div id="crawlspace-access-info"
                                             class="collapse alert alert-info remove-collapse-space">
                                            I would like to have some help full information right here !
                                        </div>
                                    </div>
                                </div>

                                @if ($errors->has('building_elements.' . $crawlspace->id .'.extra'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('building_elements.' . $crawlspace->id .'.extra') }}</strong>
                                </span>
                                @endif

                                <div id="crawlspace-no-access-error" class="help-block" style="display: none;">
                                    <div class="alert alert-warning show" role="alert">
                                        <p>@lang('woningdossier.cooperation.tool.floor-insulation.crawlspace-access.no-access')</p>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="col-sm-12 col-md-6">
                            <div class="form-group add-space{{ $errors->has('building_elements.' . $crawlspace->id .'.element_value_id') ? ' has-error' : '' }}">
                                <label for="crawlspace_height" class=" control-label">
                                    <i data-toggle="collapse" data-target="#crawlspace-height-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    @lang('woningdossier.cooperation.tool.floor-insulation.crawlspace-height')
                                </label>

                                <select id="crawlspace_height" class="form-control" name="building_elements[{{ $crawlspace->id }}][element_value_id]">
                                    @foreach($crawlspace->values as $crawlHeight)
                                        <option @if(old('crawlspace_height') == $crawlHeight->id) selected
                                                @elseif(
                                            isset($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                            && $buildingElement->where('element_id', $crawlspace->id)->first()->element_value_id == $crawlHeight->id)
                                                selected
                                            @endif value="{{ $crawlHeight->id }}">{{ $crawlHeight->value }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('building_elements.' . $crawlspace->id .'.element_value_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('building_elements.' . $crawlspace->id .'.element_value_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group add-space">
                                <div id="crawlspace-height-info" class="collapse alert alert-info remove-collapse-space">
                                    I would like to have some help full information right here !
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row crawlspace-accessible">
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('building_features.floor_surface') ? ' has-error' : '' }}">

                            <label for="floor_surface" class=" control-label">
                                <i data-toggle="collapse" data-target="#floor-surface-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.floor-insulation.floor-surface')
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                                <input type="text" name="building_features[floor_surface]" class="form-control" value="{{ old('building_features.floor_surface', $buildingFeatures->floor_surface) }}">
                            </div>
                            @if ($errors->has('building_features.floor_surface'))
                                <span class="help-block">
                                <strong>{{ $errors->first('building_features.floor_surface') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group add-space">
                            <div id="floor-surface-info" class="collapse alert alert-info remove-collapse-space">
                                I would like to have some help full information right here !
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('comment') ? ' has-error' : '' }}">
                            <label for="additional-info" class=" control-label"><i data-toggle="collapse" data-target="#additional-info-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('default.form.input.comment')        </label>
                            <?php

                                $default = isset($buildingElement->where('element_id', $crawlspace->id)->first()->extra['comment']) ? $buildingElement->where('element_id', $crawlspace->id)->first()->extra['comment'] : "";
                            ?>

                            <textarea name="comment" id="" class="form-control">{{old('comment', $default)}}</textarea>

                            <div id="additional-info-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                I would like to have some helpful information right here!
                            </div>

                            @if ($errors->has('comment'))
                                <span class="help-block">
                                <strong>{{ $errors->first('comment') }}</strong>
                            </span>
                            @endif

                        </div>
                    </div>
                </div>


                <div class="row crawlspace-accessible">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-info show" role="alert">
                            <p>@lang('woningdossier.cooperation.tool.floor-insulation.insulation-advice.text')</p>
                            <p id="insulation-advice"></p>
                        </div>
                    </div>
                </div>

                <div id="indication-for-costs" class="crawlspace-accessible">
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
                                    <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.kilograms')  / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
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
            </div>

            <div class="row" id="no-crawlspace-error">
                <div class="col-md-12">
                    <div class="alert alert-danger show" role="alert">
                        <p>@lang('woningdossier.cooperation.tool.floor-insulation.has-crawlspace.no-crawlspace')</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">@lang('default.buttons.download')</div>
                        <div class="panel-body">
                            <ol>
                                <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Vloerisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Vloerisolatie.pdf')))))}}</a></li>
                                <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Bodemisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Bodemisolatie.pdf')))))}}</a></li>
                                <?php $helpFile = "storage/hoomdossier-assets/Invul_hulp_Vloerisolatie.pdf"; ?>
                                <li><a download="" href="{{asset($helpFile)}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset($helpFile)))))}}</a></li>

                            </ol>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group add-space">
                        <div class="">
                            <a class="btn btn-success pull-left"
                               href="{{route('cooperation.tool.insulated-glazing.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                            <button type="submit" class="btn btn-primary pull-right">
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

            crawlspaceOptions();

            $('#has_crawlspace').change(crawlspaceOptions);
            $("select, input[type=radio], input[type=text]").change(formChange);

            function crawlspaceOptions(){
                if ($("#has_crawlspace").val() === "no"){
                    $(".crawlspace-accessible").hide();
                    $("#no-crawlspace-error").show();
                }
                else {
                    $(".crawlspace-accessible").show();
                    $("#no-crawlspace-error").hide();
                }
            }

            function formChange(){
                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.floor-insulation.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function(data){
                        if (data.insulation_advice){
                            $("#insulation-advice").html("<strong>" + data.insulation_advice + "</strong>");
                        }
                        else {
                            $("#insulation-advice").html("");
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
                        if (data.hasOwnProperty('crawlspace')){
                            $("#crawlspace-unknown-error").show();
                        }
                        else {
                            $("#crawlspace-unknown-error").hide();
                        }



                        @if(App::environment('local'))
                        console.log(data);
                        @endif

                        if ($("#floor-insulation-options select option:selected").text() == "Niet van toepassing") {
                            $('#answers').hide();
                            $("input#savings_gas").val(Math.round(0));
                            $("input#savings_co2").val(Math.round(0));
                            $("input#savings_money").val(Math.round(0));
                            $("input#cost_indication").val(Math.round(0));
                            $("input#interest_comparable").val(0);

                        } else {
                            $('#answers').show();
                        }
                    }
                });
            }

            $('form').find('*').filter(':input:visible:first').trigger('change');


        });
    </script>
@endpush

