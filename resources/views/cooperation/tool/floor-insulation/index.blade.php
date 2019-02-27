@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('floor-insulation.title.title'))


@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.floor-insulation.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}

        @include('cooperation.tool.includes.interested', [
            'type' => 'element', 'buildingElements' => $floorInsulation, 'buildingElement' => 'floor-insulation'
        ])


        <div id="floor-insulation">
            <div class="row">
                <div class="col-sm-12">
                    @include('cooperation.layouts.section-title', ['translationKey' => 'floor-insulation.intro.title'])
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('element.' . $floorInsulation->id) ? ' has-error' : '' }}">

                        <label for="element_{{ $floorInsulation->id }}" class="control-label">
                            <i data-toggle="collapse" data-target="#floor-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('floor-insulation.floor-insulation.title')}}
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $floorInsulation->values()->orderBy('order')->get(), 'userInputValues' => $buildingInsulationForMe ,'userInputColumn' => 'element_value_id'])
                            <div id="floor-insulation-options">
                                <select id="element_{{ $floorInsulation->id }}" class="form-control" name="element[{{ $floorInsulation->id }}]">
                                    @foreach($floorInsulation->values()->orderBy('order')->get() as $elementValue)
                                        <option data-calculate-value="{{$elementValue->calculate_value}}" @if(old('element.' . $floorInsulation->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $floorInsulation->id), 'element_value_id')) == $elementValue->id) selected="selected" @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                                        {{--<option
                                        <option data-calculate-value="{{$elementValue->calculate_value}}"
                                                @if(old('element.' . $floorInsulation->id . '') && $floorInsulation->id == old('element.' . $floorInsulation->id . ''))
                                                selected="selected"
                                                @elseif(isset($buildingFeature->element_values) && $elementValue->id == $buildingFeature->element_values)
                                                selected="selected"
                                                @elseif(isset($buildingInsulation->element_value_id) && $elementValue->id == $buildingInsulation->element_value_id)
                                                selected="selected"
                                                @endif
                                                value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>--}}
                                    @endforeach
                                </select>
                            </div>
                        @endcomponent

                        @if ($errors->has('element.' . $floorInsulation->id))
                            <span class="help-block">
                                <strong>{{ $errors->first('element.' . $floorInsulation->id) }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div class="col-sm-12">
                    <div class="form-group add-space">
                        <div id="floor-insulation-info" class="collapse alert alert-info remove-collapse-space">
                            {{\App\Helpers\Translation::translate('floor-insulation.floor-insulation.help')}}
                        </div>
                    </div>
                </div>
            </div>

            @include('cooperation.tool.includes.savings-alert', ['buildingElement' => 'floor-insulation'])

            <div id="hideable">

                <div id="answers">

                    <div class="row">
                        <div class="col-sm-12">
                            <div id="has-no-crawlspace"
                                 class="form-group add-space{{ $errors->has('building_elements.crawlspace') ? ' has-error' : '' }}">
                                <label for="has_crawlspace" class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_elements-crawlspace-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    {{\App\Helpers\Translation::translate('floor-insulation.has-crawlspace.title')}}
                                </label>

                                @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => __('woningdossier.cooperation.option'), 'userInputValues' => $buildingElementsForMe->where('element_id', $crawlspace->id) ,'userInputColumn' => 'extra.has_crawlspace'])<select id="has_crawlspace" class="form-control" name="building_elements[crawlspace]">
                                    @foreach(__('woningdossier.cooperation.option') as $i => $option)
                                        <option @if(old('building_elements.crawlspace', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $crawlspace->id), 'extra.has_crawlspace')) == $i) selected="selected" @endif value="{{ $i }}">{{ $option }}</option>
                                        {{--<option @if(old('building_elements.crawlspace') == $i) selected
                                                @elseif(isset($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                            && is_array($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                            && array_key_exists('has_crawlspace', $buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                            && $buildingElement->where('element_id', $crawlspace->id)->first()->extra['has_crawlspace'] == $i ) selected
                                                @endif value="{{ $i }}">{{ $option }}</option>--}}
                                    @endforeach
                                </select>@endcomponent

                                <div class="col-sm-12">
                                    <div class="form-group add-space">
                                        <div id="building_elements-crawlspace-info" class="collapse alert alert-info remove-collapse-space">
                                            {{\App\Helpers\Translation::translate('floor-insulation.has-crawlspace.help')}}
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
                                        <p>@lang('woningdossier.cooperation.tool.floor-insulation.crawlspace-access.no-access')</p>
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
                                        {{\App\Helpers\Translation::translate('floor-insulation.crawlspace-access.title')}}
                                    </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => __('woningdossier.cooperation.option'), 'userInputValues' => $buildingElementsForMe->where('element_id', $crawlspace->id) ,'userInputColumn' => 'extra.access'])<select id="crawlspace_access" class="form-control" name="building_elements[{{ $crawlspace->id }}][extra]">
                                    @foreach(__('woningdossier.cooperation.option') as $i => $option)
                                        <option @if(old('building_elements.crawlspace_access', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $crawlspace->id), 'extra.access')) == $i) selected="selected" @endif value="{{ $i }}">{{ $option }}</option>
                                            {{--<option @if(old('building_elements.crawlspace_access') == $option) selected
                                                @elseif(isset($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                                &&is_array($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                                && array_key_exists('access', $buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                                && $buildingElement->where('element_id', $crawlspace->id)->first()->extra['access'] == $i) selected
                                                @endif value="{{ $i }}">{{ $option }}</option>--}}
                                    @endforeach
                                </select>@endcomponent

                                    <div class="col-sm-12">
                                        <div class="form-group add-space">
                                            <div id="crawlspace-access-info"
                                                 class="collapse alert alert-info remove-collapse-space">
                                                {{\App\Helpers\Translation::translate('floor-insulation.crawlspace-access.help')}}
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
                                            {{--<p>{{\App\Helpers\Translation::translate('floor-insulation.crawlspace-access.no-access.title')}}</p>--}}
                                            <p>@lang('woningdossier.cooperation.tool.floor-insulation.crawlspace-access.no-access')</p>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <div class="col-sm-12 col-md-6">
                                <div class="form-group add-space{{ $errors->has('building_elements.' . $crawlspace->id .'.element_value_id') ? ' has-error' : '' }}">
                                    <label for="crawlspace_height" class=" control-label">
                                        <i data-toggle="collapse" data-target="#crawlspace-height-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                        {{\App\Helpers\Translation::translate('floor-insulation.crawlspace-height.title')}}
                                    </label>

                                    @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $crawlspace->values, 'userInputValues' => $buildingElementsForMe->where('element_id', $crawlspace->id) ,'userInputColumn' => 'element_value_id'])<select id="crawlspace_height" class="form-control" name="building_elements[{{ $crawlspace->id }}][element_value_id]">
                                        @foreach($crawlspace->values as $crawlHeight)
                                            <option @if(old('crawlspace_height', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $crawlspace->id), 'element_value_id')) == $crawlHeight->id) selected="selected" @endif value="{{ $crawlHeight->id }}">{{ $crawlHeight->value }}</option>
                                            {{--<option @if(old('crawlspace_height') == $crawlHeight->id) selected
                                                    @elseif(
                                                isset($buildingElement->where('element_id', $crawlspace->id)->first()->extra)
                                                && $buildingElement->where('element_id', $crawlspace->id)->first()->element_value_id == $crawlHeight->id)
                                                    selected
                                                @endif value="{{ $crawlHeight->id }}">{{ $crawlHeight->value }}</option>--}}
                                        @endforeach
                                    </select>@endcomponent


                            <div class="col-sm-12">

                            <div class="form-group add-space">
                                    <div id="crawlspace-height-info" class="collapse alert alert-info remove-collapse-space">
                                        {{\App\Helpers\Translation::translate('floor-insulation.crawlspace-height.help')}}</div>
                                    </div>
                                </div>

                                @if ($errors->has('building_elements.' . $crawlspace->id .'.element_value_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('building_elements.' . $crawlspace->id .'.element_value_id') }}</strong>
                                </span>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row crawlspace-accessible">
                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('building_features.floor_surface') ? ' has-error' : '' }}">

                                <label for="surface" class=" control-label">
                                    <i data-toggle="collapse" data-target="#floor-surface-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    {{\App\Helpers\Translation::translate('floor-insulation.surface.title')}}
                                </label>
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'floor_surface', 'needsFormat' => true])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                                    <input id="floor_surface" type="text" name="building_features[floor_surface]" value="{{ \App\Helpers\NumberFormatter::format(old('building_features.floor_surface', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'floor_surface')),1) }}" class="form-control" >
                                {{--<input id="floor_surface" type="text" name="building_features[floor_surface]" class="form-control" value="{{ old('building_features.floor_surface', \App\Helpers\NumberFormatter::format($buildingFeatures->floor_surface, 1)) }}">--}}
                                @endcomponent
                                @if ($errors->has('building_features.surface'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('building_features.floor_surface') }}</strong>
                                </span>
                                @endif</div>
                                <div class="form-group add-space">
                                    <div id="floor-surface-info" class="collapse alert alert-info remove-collapse-space">
                                        {{\App\Helpers\Translation::translate('floor-insulation.surface.help')}}
                                    </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('building_features.insulation_surface') ? ' has-error' : '' }}">

                                <label for="insulation_floor_surface" class=" control-label">
                                    <i data-toggle="collapse" data-target="#floor-insulation-surface-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    {{\App\Helpers\Translation::translate('floor-insulation.insulation-surface.title')}}
                                </label>
                                @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'insulation_surface', 'needsFormat' => true])
                                    <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                                    <input id="insulation_floor_surface" type="text" name="building_features[insulation_surface]" value="{{ \App\Helpers\NumberFormatter::format(old('building_features.insulation_surface', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'insulation_surface')),1) }}" class="form-control" >
                                {{--<input id="insulation_floor_surface" type="text" name="building_features[insulation_surface]" class="form-control" value="{{ old('building_features.insulation_surface', \App\Helpers\NumberFormatter::format($buildingFeatures->insulation_surface, 1)) }}">--}}
                                @endcomponent
                                @if ($errors->has('building_features.insulation_surface'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('building_features.insulation_surface') }}</strong>
                                </span>
                                @endif
                            </div>


                        <div class="form-group add-space">
                            <div id="floor-insulation-surface-info" class="collapse alert alert-info remove-collapse-space">
                                {{\App\Helpers\Translation::translate('floor-insulation.insulation-surface.help')}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="row additional-info">
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('comment') ? ' has-error' : '' }}">
                            <label for="additional-info" class=" control-label"><i data-toggle="collapse" data-target="#additional-info-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>{{\App\Helpers\Translation::translate('general.specific-situation.title')}}        </label>
                            <?php
                                $default = isset($buildingElement->where('element_id', $crawlspace->id)->first()->extra['comment']) ? $buildingElement->where('element_id', $crawlspace->id)->first()->extra['comment'] : '';
                            ?>

                            <textarea name="comment" id="" class="form-control">{{old('comment', $default)}}</textarea>

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


                <div class="row crawlspace-accessible">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-info show" role="alert">
                            <p>{{\App\Helpers\Translation::translate('floor-insulation.insulation-advice.text.title')}}</p>
                            <p id="insulation-advice"></p>
                        </div>
                    </div>
                </div>

                <div id="indication-for-costs" class="crawlspace-accessible">
                    <hr>
                    <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('floor-insulation.indication-for-costs.title')}}</h4>

                    <div id="costs" class="row">
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
                        <div class="col-sm-4">
                            @include('cooperation.layouts.indication-for-costs.indicative-costs')
                        </div>
                        <div class="col-sm-4">
                            @include('cooperation.layouts.indication-for-costs.comparable-rent')
                        </div>
                    </div>
                </div>
            </div>
            @include('cooperation.tool.includes.comment', [
               'collection' => $buildingElementsForMe->where('element_id', $crawlspace->id),
               'commentColumn' => 'extra.comment',
               'translation' => [
                   'title' => 'general.specific-situation.title',
                   'help' => 'general.specific-situation.help'
               ]
            ])

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
        </div>
    </form>
@endsection

@push('js')
    <script>

        $(document).ready(function() {

            $(window).keydown(function(event){
                if(event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $("select, input[type=radio], input[type=text]").change(formChange);

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

                        if ($("#floor-insulation-options select option:selected").data('calculate-value') === 5) {
                            $("input#savings_gas").val(Math.round(0));
                            $("input#savings_co2").val(Math.round(0));
                            $("input#savings_money").val(Math.round(0));
                            $("input#cost_indication").val(Math.round(0));
                            $("input#interest_comparable").val(0);

                        }

                        // Reset display: properties
                        resetDisplays();
                        // Apply crawlspace options
                        crawlspaceOptions();
                        // Apply interest options
                        checkInterestAndCurrentInsulation();
                    }
                });

            }

            function resetDisplays(){
                $("#hideable").show();
                $("#answers").show();
                $("#has-no-crawlspace").show();
            }

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

            function checkInterestAndCurrentInsulation(){
                var interestedCalculateValue = $('#interest_element_{{$floorInsulation->id}} option:selected').data('calculate-value');
                var elementCalculateValue = $('#element_{{$floorInsulation->id}} option:selected').data('calculate-value');

                if (elementCalculateValue === 5){
                    // nvt
                    $(".crawlspace-accessible").hide();
                    $("#has-no-crawlspace").hide();
                    $("#no-crawlspace-error").hide();
                    $('#floor-insulation-info-alert').find('.alert').addClass('hide');
                }
                else {
                    if ((elementCalculateValue === 3 || elementCalculateValue === 4)/* && interestedCalculateValue <= 2*/) {
                        // insulation already present and there's interest
                        $('#hideable').hide();
                        $('#floor-insulation-info-alert').find('.alert').removeClass('hide');
                    } else {
                        $('#hideable').show();
                        //$("#has-no-crawlspace").show();
                        //crawlspaceOptions();
                        $('#floor-insulation-info-alert').find('.alert').addClass('hide');
                    }
                }
            }

            //$('form').find('*').filter(':input:visible:first').trigger('change');
            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');

        });

        $('#floor_surface').on('change', function () {
            if ($('#insulation_floor_surface').val().length == 0 || $('#insulation_floor_surface').val() == "0,0" || $('#insulation_floor_surface').val() == "0.00") {
                $('#insulation_floor_surface').val($('#floor_surface').val())
            }
        });

    </script>
@endpush

