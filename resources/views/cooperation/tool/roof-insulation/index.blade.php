@extends('cooperation.frontend.layouts.tool')

@section('step_title', __('roof-insulation.title.title'))

@section('content')
    <form method="POST" id="roof-insulation-form"
          action="{{ route('cooperation.tool.roof-insulation.store', compact('cooperation')) }}">
        @csrf


        <div class="flex flex-row flex-wrap w-full">
            <div id="current-situation" class="w-full">

                <div class="flex flex-row flex-wrap w-full">
                    <div class="w-full">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'building_roof_types', 'name' => 'building_roof_type_ids',
                            'translation' => 'roof-insulation.current-situation.roof-types',
                            'inputGroupClass' => 'pad-x-3',
                        ])
                            @slot('sourceSlot')
                                @include('cooperation.tool.components.source-list', [
                                    'inputType' => 'checkbox', 'inputValues' => $roofTypes,
                                    'userInputValues' => $currentRoofTypesForMe, 'userInputColumn' => 'roof_type_id'
                                ])
                            @endslot

                            @foreach($roofTypes as $roofType)
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" data-calculate-value="{{$roofType->calculate_value}}"
                                           id="building-roof-type-{{$roofType->id}}"
                                           name="building_roof_type_ids[]"
                                           value="{{$roofType->id}}"
                                           @if(in_array($roofType->id, old('building_roof_type_ids',[ \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'roof_type_id', null, \App\Helpers\Hoomdossier::getMostCredibleInputSource($building->roofTypes())) ]))) checked="checked" @endif>
                                    <label for="building-roof-type-{{$roofType->id}}">
                                        <span class="checkmark"></span>
                                        <span>{{ $roofType->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        @endcomponent
                    </div>
                </div>

                <div class="if-roof">
                    <div class="flex flex-row flex-wrap w-full">
                        <div class="w-full">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'building_features.roof_type_id', 
                                'translation' => 'roof-insulation.current-situation.main-roof', 'required' => false
                            ])
                                @slot('sourceSlot')
                                    @include('cooperation.tool.components.source-list', [
                                        'inputType' => 'select', 'inputValues' => $roofTypes, 
                                        'userInputValues' => $building->buildingFeatures()->forMe()->get(), 
                                        'userInputModel' => 'roofType', 'userInputColumn' => 'roof_type_id'
                                    ])
                                @endslot

                                @component('cooperation.frontend.layouts.components.alpine-select')
                                    <select id="main_roof" class="form-input"
                                            name="building_features[roof_type_id]">
                                        @foreach($roofTypes as $roofType)
                                            <option @if(old('building_features.roof_type_id', Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'roof_type_id')) == $roofType->id) selected="selected"
                                                    @endif value="{{ $roofType->id }}">
                                                {{ $roofType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endcomponent
                            @endcomponent
                        </div>
                    </div>

                    @foreach($roofTypes->whereIn('short', ['flat', 'pitched']) as $roofType)

                        <?php
                        $roofCat = $roofType->short;

                        $buildingRoofTypesOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
                            $building->roofTypes()->where('roof_type_id', $roofType->id)
                        )->get();
                        ?>

                        <div class="{{ $roofCat }}-roof">

                            @include('cooperation.tool.includes.section-title', [
                                'translation' => 'roof-insulation.'.$roofCat.'-roof.situation-title',
                                'id' => 'roof-situation-title'
                            ])
                            <div class="flex flex-row flex-wrap w-full">
                            <!-- is the {{ $roofCat }} roof insulated? -->
                                <div class="w-full">
                                    @component('cooperation.tool.components.step-question', [
                                        'id' => $roofCat .'_roof_insulation',
                                        'name' => 'building_roof_types.' . $roofCat . '.element_value_id',
                                        'translation' => 'roof-insulation.current-situation.is-'.$roofCat.'-roof-insulated'
                                    ])
                                        @slot('sourceSlot')
                                            @include('cooperation.tool.components.source-list', [
                                                'inputType' => 'select', 'inputValues' => $roofInsulation->values,
                                                'userInputValues' => $buildingRoofTypesOrderedOnInputSourceCredibility,
                                                'userInputColumn' => 'element_value_id'
                                            ])
                                        @endslot

                                        @component('cooperation.frontend.layouts.components.alpine-select')
                                            <select id="{{ $roofCat }}_roof_insulation" class="form-input"
                                                    name="building_roof_types[{{ $roofCat }}][element_value_id]">
                                                @foreach($roofInsulation->values as $insulation)
                                                    @if($insulation->calculate_value < 6)
                                                        <option data-calculate-value="{{$insulation->calculate_value}}"
                                                                @if($insulation->id == old('building_roof_types.' . $roofCat . '.element_value_id', Hoomdossier::getMostCredibleValueFromCollection($buildingRoofTypesOrderedOnInputSourceCredibility, 'element_value_id'))) selected="selected"
                                                                @endif value="{{ $insulation->id }}">
                                                            {{ $insulation->value }}
                                                        </option>
                                                        {{--<option data-calculate-value="{{$insulation->calculate_value}}" @if($insulation->id == old('building_roof_types.' . $roofCat . '.element_value_id') || (isset($currentCategorizedRoofTypes[$roofCat]['element_value_id']) && $currentCategorizedRoofTypes[$roofCat]['element_value_id'] == $insulation->id)) selected @endif value="{{ $insulation->id }}">{{ $insulation->value }}</option>--}}
                                                    @endif
                                                @endforeach
                                            </select>
                                        @endcomponent
                                    @endcomponent
                                </div>
                            </div>
                            @include('cooperation.tool.includes.savings-alert', ['buildingElement' => $roofCat])
                            {{--<div class="{{$roofCat}}-hideable">--}}
                            <div class="flex flex-row flex-wrap w-full">
                                <div class="w-full md:w-1/2 md:pr-3 roof-surface-inputs">
                                    @component('cooperation.tool.components.step-question', [
                                        'id' => 'building_roof_types.' . $roofCat . '.roof_surface',
                                        'translation' => 'roof-insulation.current-situation.'.$roofCat.'-roof-surface',
                                        'required' => true
                                    ])
                                        @slot('sourceSlot')
                                            @include('cooperation.tool.components.source-list', [
                                                'inputType' => 'input', 
                                                'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 
                                                'userInputColumn' => 'roof_surface'
                                            ])
                                        @endslot

                                        <span class="input-group-prepend">@lang('general.unit.square-meters.title')</span>
                                        <input type="text" class="form-input" required="required"
                                               name="building_roof_types[{{ $roofCat }}][roof_surface]"
                                               value="{{ old('building_roof_types.' . $roofCat . '.roof_surface', Hoomdossier::getMostCredibleValueFromCollection($buildingRoofTypesOrderedOnInputSourceCredibility, 'roof_surface')) }}">
                                        {{--<input type="text" class="form-input" name="building_roof_types[{{ $roofCat }}][roof_surface]" value="{{isset($currentCategorizedRoofTypes[$roofCat]['roof_surface']) ? $currentCategorizedRoofTypes[$roofCat]['roof_surface'] : old('building_roof_types.' . $roofCat . '.roof_surface')}}">--}}
                                    @endcomponent
                                </div>

                                <div class="w-full md:w-1/2 md:pl-3">
                                    @component('cooperation.tool.components.step-question', [
                                        'id' => 'building_roof_types.' . $roofCat . '.insulation_roof_surface',
                                        'translation' => 'roof-insulation.current-situation.insulation-'.$roofCat.'-roof-surface',
                                        'required' => true
                                    ])
                                        @slot('sourceSlot')
                                            @include('cooperation.tool.components.source-list', [
                                                'inputType' => 'input',
                                                'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat],
                                                'userInputColumn' => 'insulation_roof_surface'
                                            ])
                                        @endslot

                                        <span class="input-group-prepend">@lang('general.unit.square-meters.title')</span>
                                        <input type="text" required="required" class="form-input"
                                               name="building_roof_types[{{ $roofCat }}][insulation_roof_surface]"
                                               value="{{ old('building_roof_types.' . $roofCat . '.insulation_roof_surface', Hoomdossier::getMostCredibleValueFromCollection($buildingRoofTypesOrderedOnInputSourceCredibility, 'insulation_roof_surface')) }}">
                                        {{--<input type="text"  class="form-input" name="building_roof_types[{{ $roofCat }}][insulation_roof_surface]" value="{{isset($currentCategorizedRoofTypes[$roofCat]['insulation_roof_surface']) ? $currentCategorizedRoofTypes[$roofCat]['insulation_roof_surface'] : old('building_roof_types.' . $roofCat . '.insulation_roof_surface')}}">--}}
                                    @endcomponent
                                </div>
                            </div>

                            <div class="flex flex-row flex-wrap w-full">
                                <div class="w-full">
                                    @component('cooperation.tool.components.step-question', [
                                        'id' => 'building_roof_types.' . $roofCat . '.extra.zinc_replaced_date',
                                        'translation' => 'roof-insulation.current-situation.zinc-replaced',
                                        'required' => false
                                    ])

                                        @slot('sourceSlot')
                                            @include('cooperation.tool.components.source-list', [
                                                'inputType' => 'input',
                                                'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat],
                                                'userInputColumn' => 'extra.zinc_replaced_date'
                                            ])
                                        @endslot

                                        <span class="input-group-prepend">@lang('general.unit.year.title')</span>
                                        <input type="text" class="form-input"
                                               name="building_roof_types[{{ $roofCat }}][extra][zinc_replaced_date]"
                                               value="{{ old('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date', Hoomdossier::getMostCredibleValueFromCollection($buildingRoofTypesOrderedOnInputSourceCredibility, 'extra.zinc_replaced_date')) }}">
                                    @endcomponent
                                </div>
                            </div>
                            <div class="flex flex-row flex-wrap w-full cover-bitumen">
                                <div class="w-full">
                                    @component('cooperation.tool.components.step-question', [
                                        'id' => 'building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date',
                                        'translation' => 'roof-insulation.current-situation.bitumen-insulated',
                                        'required' => false
                                    ])
                                        @slot('sourceSlot')
                                            @include('cooperation.tool.components.source-list', [
                                                'inputType' => 'input',
                                                'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat],
                                                'userInputColumn' => 'extra.bitumen_replaced_date'
                                            ])
                                        @endslot

                                        <span class="input-group-prepend">@lang('general.unit.year.title')</span>
                                        <input type="text" class="form-input"
                                               name="building_roof_types[{{ $roofCat }}][extra][bitumen_replaced_date]"
                                               value="{{ old('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date', Hoomdossier::getMostCredibleValueFromCollection($buildingRoofTypesOrderedOnInputSourceCredibility, 'extra.bitumen_replaced_date')) }}">
                                        {{--<input type="text" class="form-input" name="building_roof_types[{{ $roofCat }}][extra][bitumen_replaced_date]" value="{{ old('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date', $default) }}">--}}
                                    @endcomponent
                                </div>
                            </div>

                            @if($roofCat == 'pitched')
                                <div class="flex flex-row flex-wrap w-full cover-tiles">
                                    <div class="w-full">
                                        @component('cooperation.tool.components.step-question', [
                                            'id' => 'building_roof_types.' . $roofCat . '.extra.tiles_condition',
                                            'translation' => 'roof-insulation.current-situation.in-which-condition-tiles',
                                            'required' => false
                                        ])
                                            @slot('sourceSlot')
                                                @include('cooperation.tool.components.source-list', [
                                                    'inputType' => 'select', 'inputValues' => $roofTileStatuses,
                                                    'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat],
                                                    'userInputColumn' => 'extra.tiles_condition'
                                                ])
                                            @endslot

                                            @component('cooperation.frontend.layouts.components.alpine-select')
                                                <select id="tiles_condition" class="form-input"
                                                        name="building_roof_types[{{ $roofCat }}][extra][tiles_condition]">
                                                    @foreach($roofTileStatuses as $roofTileStatus)
                                                        <option @if($roofTileStatus->id == old('building_roof_types.' . $roofCat . '.extra.tiles_condition', Hoomdossier::getMostCredibleValueFromCollection($buildingRoofTypesOrderedOnInputSourceCredibility, 'extra.tiles_condition'))) selected="selected"
                                                                @endif value="{{ $roofTileStatus->id }}">
                                                            {{ $roofTileStatus->name }}
                                                        </option>
                                                        {{--<option @if($roofTileStatus->id == old('building_roof_types.' . $roofCat . '.extra.tiles_condition', $default)) selected  @endif value="{{ $roofTileStatus->id }}">{{ $roofTileStatus->name }}</option>--}}
                                                    @endforeach
                                                </select>
                                            @endcomponent
                                        @endcomponent
                                    </div>
                                </div>
                            @endif

                            <div class="{{$roofCat}}-hideable">
                                <div class="flex flex-row flex-wrap w-full">
                                    <div class="w-full md:w-1/2 md:pr-3">
                                        @component('cooperation.tool.components.step-question', [
                                            'id' => 'building_roof_types.' . $roofCat . '.extra.measure_application_id',
                                            'translation' => 'roof-insulation.'.$roofCat.'-roof.insulate-roof',
                                            'required' => false
                                        ])
                                            <?php
                                                $default = isset($currentCategorizedRoofTypes[$roofCat]['extra']['measure_application_id']) ? $currentCategorizedRoofTypes[$roofCat]['extra']['measure_application_id'] : 0;
                                            ?>
                                            @slot('sourceSlot')
                                                @include('cooperation.tool.components.source-list', [
                                                    'inputType' => 'select', 'inputValues' => $measureApplications[$roofCat],
                                                    'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat],
                                                    'userInputColumn' => 'extra.measure_application_id',
                                                    'customInputValueColumn' => 'measure_name'
                                                ])
                                            @endslot

                                            @component('cooperation.frontend.layouts.components.alpine-select')
                                                <select id="flat_roof_insulation" class="form-input"
                                                        name="building_roof_types[{{ $roofCat }}][extra][measure_application_id]">
                                                    <option value="0" @if($default == 0) selected @endif>
                                                        @lang('roof-insulation.measure-application.no.title')
                                                    </option>
                                                    @foreach($measureApplications[$roofCat] as $measureApplication)
                                                        <option @if($measureApplication->id == old("building_roof_types.{$roofCat}.extra.measure_application_id", Hoomdossier::getMostCredibleValueFromCollection($buildingRoofTypesOrderedOnInputSourceCredibility, 'extra.measure_application_id'))) selected="selected"
                                                                @endif value="{{ $measureApplication->id }}">
                                                            {{ $measureApplication->measure_name }}
                                                        </option>
                                                        {{--<option @if($measureApplication->id == old('building_roof_types.' . $roofCat . '.extra.measure_application_id', $default)) selected @endif value="{{ $measureApplication->id }}">{{ $measureApplication->measure_name }}</option>--}}
                                                    @endforeach
                                                </select>
                                            @endcomponent
                                        @endcomponent
                                    </div>
                                    <div class="w-full md:w-1/2 md:pl-3">
                                        @component('cooperation.tool.components.step-question', [
                                            'id' => 'building_roof_types.' . $roofCat . '.building_heating_id',
                                            'translation' => 'roof-insulation.'.$roofCat.'-roof.situation',
                                            'required' => false
                                        ])
                                            @slot('sourceSlot')
                                                @include('cooperation.tool.components.source-list', [
                                                    'inputType' => 'select', 'inputValues' => $heatings,
                                                    'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat],
                                                    'userInputColumn' => 'building_heating_id'
                                                ])
                                            @endslot

                                            @component('cooperation.frontend.layouts.components.alpine-select')
                                                <select id="flat_roof_situation" class="form-input"
                                                        name="building_roof_types[{{ $roofCat }}][building_heating_id]">
                                                    @foreach($heatings as $heating)
                                                        @if($heating->calculate_value < 5)
                                                            <option @if($heating->id == old('building_roof_types.' . $roofCat . '.building_heating_id', Hoomdossier::getMostCredibleValueFromCollection($buildingRoofTypesOrderedOnInputSourceCredibility, 'building_heating_id'))) selected="selected"
                                                                    @endif value="{{ $heating->id }}">
                                                                {{ $heating->name }}
                                                            </option>
                                                            {{--<option @if($heating->id == old('building_roof_types.' . $roofCat . '.building_heating_id', $default)) selected @endif value="{{ $heating->id }}">{{ $heating->name }}</option>--}}
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @endcomponent
                                        @endcomponent
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @foreach(['flat', 'pitched'] as $roofCat)
            <div class="flex flex-row flex-wrap w-full">
                <div class="costs {{ $roofCat }}-roof w-full">
                    <div class="flex flex-row flex-wrap w-full">
                        <div class="w-full">
                            @include('cooperation.tool.includes.section-title', [
                                'translation' => 'roof-insulation.'.$roofCat.'.costs.title',
                                'id' => $roofCat.'costs'
                            ])
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap w-full md:pad-x-6">
                        <div class="w-full md:w-1/3 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.gas', [
                                'id' => $roofCat, 'translation' => "{$currentStep->slug}.{$roofCat}.costs.gas"
                            ])
                        </div>
                        <div class="w-full md:w-1/3 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.co2', [
                                'id' => $roofCat, 'translation' => "{$currentStep->slug}.{$roofCat}.costs.co2"
                            ])
                        </div>
                        <div class="w-full md:w-1/3 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.savings-in-euro', [
                                'id' => $roofCat, 'translation' => 'roof-insulation.index.savings-in-euro'
                            ])
                        </div>
                    </div>

                    <div class="flex flex-row flex-wrap w-full md:pad-x-6">
                        <div class="w-full md:w-1/3 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.indicative-costs', [
                                'id' => $roofCat, 'translation' => 'roof-insulation.index.indicative-costs'
                            ])
                        </div>
                        <div class="w-full md:w-1/3">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'indicative-costs-id',
                                'translation' => 'roof-insulation.'.$roofCat.'.indicative-costs-replacement',
                                'required' => false, 'withInputSource' => false,
                            ])
                                <span class="input-group-prepend"><i class="icon-sm icon-moneybag"></i></span>
                                <input type="text" id="{{ $roofCat }}_replace_cost"
                                       class="form-input disabled" disabled="" value="0">
                            @endcomponent
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap w-full md:pad-x-6">
                        <div class="w-full md:w-1/3 @if($roofCat == 'pitched') cover-tiles @endif">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'indicative-replacement-year-info',
                                'translation' => 'roof-insulation.'.$roofCat.'.indicative-replacement.year',
                                'required' => false, 'withInputSource' => false,
                            ])
                                <span class="input-group-prepend"><i class="icon-sm icon-timer"></i></span>
                                <input type="text" id="{{ $roofCat }}_replace_year"
                                       class="form-input disabled" disabled="" value="">
                            @endcomponent
                        </div>
                        <div class="w-full md:w-1/3 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.comparable-rent', [
                                'id' => $roofCat,  'translation' => 'roof-insulation.index.comparable-rent'
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


        @include('cooperation.tool.includes.comment', [
           'translation' => 'roof-insulation.index.specific-situation'
        ])

        @component('cooperation.tool.components.panel', [
            'label' => __('default.buttons.download')
        ])
            <ol>
                <li><a download=""
                       href="{{asset('storage/hoomdossier-assets/Maatregelblad_Dakisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Dakisolatie.pdf')))))}}</a>
                </li>
            </ol>
        @endcomponent
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            //$('select[name*=element_value_id]').trigger('change');

            $('#main-tab form input, select').change(() => formChange())

            function formChange() {

                let form = $('#roof-insulation-form').serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.roof-insulation.calculate', compact('cooperation')) }}',
                    data: form,
                    success: function (data) {
                        if (!data.hasOwnProperty('flat') && !data.hasOwnProperty('pitched')) {
                            $(".if-roof").hide();
                        } else {

                            $(".if-roof").show();
                        }

                        // default
                        //$(".cover-zinc").hide();
                        $(".flat-roof .cover-bitumen").hide();
                        $(".pitched-roof .cover-bitumen").hide();

                        if (data.hasOwnProperty('flat')) {
                            $(".flat-roof").show();
                            $(".flat-roof .cover-bitumen").show();


                            //if (data.flat.hasOwnProperty('type') && data.flat.type === 'zinc'){
                            //    $(".cover-zinc").show();
                            //}
                            if (data.flat.hasOwnProperty('savings_gas')) {
                                $("input#flat_savings_gas").val(hoomdossierRound(data.flat.savings_gas));
                            }
                            if (data.flat.hasOwnProperty('savings_co2')) {
                                $("input#flat_savings_co2").val(hoomdossierRound(data.flat.savings_co2));
                            }
                            if (data.flat.hasOwnProperty('savings_money')) {
                                $("input#flat_savings_money").val(hoomdossierRound(data.flat.savings_money));
                            }
                            if (data.flat.hasOwnProperty('cost_indication')) {
                                $("input#flat_cost_indication").val(hoomdossierRound(data.flat.cost_indication));
                            }
                            if (data.flat.hasOwnProperty('interest_comparable')) {
                                $("input#flat_interest_comparable").val(hoomdossierNumberFormat(data.flat.interest_comparable, '{{ app()->getLocale() }}', 1));
                            }
                            if (data.flat.hasOwnProperty('replace')) {
                                if (data.flat.replace.hasOwnProperty('year')) {
                                    $("input#flat_replace_year").val(data.flat.replace.year);
                                }
                                if (data.flat.replace.hasOwnProperty('costs')) {
                                    $("input#flat_replace_cost").val(hoomdossierRound(data.flat.replace.costs));
                                }
                            }
                        } else {
                            $(".flat-roof").hide();
                        }

                        $(".cover-tiles").hide();
                        if (data.hasOwnProperty('pitched')) {

                            $(".pitched-roof").show();
                            if (data.pitched.hasOwnProperty('type')) {

                                if (data.pitched.type === 'tiles') {
                                    $(".cover-tiles").show();
                                    $(".pitched-roof .cover-bitumen").hide();
                                }
                                if (data.pitched.type === 'bitumen') {
                                    $(".pitched-roof .cover-bitumen").show();
                                }
                            }
                            if (data.pitched.hasOwnProperty('savings_gas')) {
                                $("input#pitched_savings_gas").val(hoomdossierRound(data.pitched.savings_gas));
                            }
                            if (data.pitched.hasOwnProperty('savings_co2')) {
                                $("input#pitched_savings_co2").val(hoomdossierRound(data.pitched.savings_co2));
                            }
                            if (data.pitched.hasOwnProperty('savings_money')) {
                                $("input#pitched_savings_money").val(hoomdossierRound(data.pitched.savings_money));
                            }
                            if (data.pitched.hasOwnProperty('cost_indication')) {
                                $("input#pitched_cost_indication").val(hoomdossierRound(data.pitched.cost_indication));
                            }
                            if (data.pitched.hasOwnProperty('interest_comparable')) {
                                $("input#pitched_interest_comparable").val(hoomdossierNumberFormat(data.pitched.interest_comparable, '{{ app()->getLocale() }}', 1));
                            }
                            if (data.pitched.hasOwnProperty('replace')) {
                                if (data.pitched.replace.hasOwnProperty('year')) {
                                    $("input#pitched_replace_year").val(data.pitched.replace.year);
                                }
                                if (data.pitched.replace.hasOwnProperty('costs')) {
                                    $("input#pitched_replace_cost").val(hoomdossierRound(data.pitched.replace.costs));
                                }
                            }
                        } else {
                            $(".pitched-roof").hide();
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            }

            $('.form-input:visible:enabled').first().trigger('change');
        });


        $('input[name*="[roof_surface]"]').on('change', function () {
            var insulationRoofSurface = $(this).parents('.roof-surface-inputs').first().next().find('input[name*="[insulation_roof_surface]"]');
            if (insulationRoofSurface.length > 0) {
                if ($(insulationRoofSurface).val().length === 0 || $(insulationRoofSurface).val() === "0,0" || $(insulationRoofSurface).val() === "0.00") {
                    $(insulationRoofSurface).val($(this).val());
                }
            }
        });


        /*$('select[name^=interest]').on('change', function () {
            $('select[name*=element_value_id]').trigger('change');
        });*/

        $('select[name*=element_value_id]').on('change', function () {
            @if(App::environment('local')) console.log("element_value_id change"); @endif

            var interestedCalculateValue = $('#interest_element_{{$roofInsulation->id}} option:selected').data('calculate-value');
            var elementCalculateValue = $(this).find(':selected').data('calculate-value');

            if (elementCalculateValue >= 3 /* && interestedCalculateValue <= 2 */) {
                if ($(this).attr('name').includes('flat')) {
                    $('.flat-hideable').hide();
                    $('#flat-info-alert').find('.alert').show();
                } else if ($(this).attr('name').includes('pitched')) {
                    $('.pitched-hideable').hide();
                    $('#pitched-info-alert').find('.alert').show();
                }
            } else {
                if ($(this).attr('name').includes('flat')) {
                    $('.flat-hideable').show();
                    $('#flat-info-alert').find('.alert').hide();
                } else if ($(this).attr('name').includes('pitched')) {
                    $('.pitched-hideable').show();
                    $('#pitched-info-alert').find('.alert').hide();
                }
            }
        });
    </script>
@endpush