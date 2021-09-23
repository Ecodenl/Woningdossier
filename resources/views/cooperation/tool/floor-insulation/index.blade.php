@extends('cooperation.frontend.layouts.tool')

@section('step_title', __('floor-insulation.title.title'))

@section('content')
    <form method="POST" id="floor-insulation-form"
          action="{{ route('cooperation.tool.floor-insulation.store', compact('cooperation')) }}">
        @csrf

        @include('cooperation.tool.includes.considerable', ['considerable' => $currentStep])
        <div id="floor-insulation">
            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'element.' . $floorInsulation->id, 
                        'translation' => 'floor-insulation.floor-insulation', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select', 
                                'inputValues' => $floorInsulation->values()->orderBy('order')->get(), 
                                'userInputValues' => $buildingInsulationForMe,
                                'userInputColumn' => 'element_value_id'
                            ])
                        @endslot
                    
                        <div id="floor-insulation-options" class="w-full">
                            @component('cooperation.frontend.layouts.components.alpine-select')
                                <select id="element_{{ $floorInsulation->id }}" class="form-input"
                                        name="element[{{ $floorInsulation->id }}]">
                                    @foreach($floorInsulation->values()->orderBy('order')->get() as $elementValue)
                                        <option data-calculate-value="{{$elementValue->calculate_value}}"
                                                @if(old('element.' . $floorInsulation->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $floorInsulation->id), 'element_value_id')) == $elementValue->id) selected="selected"
                                                @endif value="{{ $elementValue->id }}">
                                            {{ $elementValue->value }}
                                        </option>
                                    @endforeach
                                </select>
                            @endcomponent
                        </div>
                    @endcomponent
                </div>
            </div>

        </div>

        @include('cooperation.tool.includes.savings-alert', ['buildingElement' => 'floor-insulation'])

        <div id="hideable">
            <div id="answers">
                <div class="flex flex-row flex-wrap w-full">
                    <div class="w-full">
                        <div id="has-no-crawlspace">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'building_elements.extra.crawlspace',
                                'translation' => 'floor-insulation.has-crawlspace', 'required' => false
                            ])
                                @slot('sourceSlot')
                                    @include('cooperation.tool.components.source-list', [
                                        'inputType' => 'select',
                                        'inputValues' => __('woningdossier.cooperation.option'),
                                        'userInputValues' => $buildingElementsOrderedOnInputSourceCredibility,
                                        'userInputColumn' => 'extra.has_crawlspace'
                                    ])
                                @endslot

                                @component('cooperation.frontend.layouts.components.alpine-select')
                                    <select id="has_crawlspace" class="form-input"
                                            name="building_elements[extra][has_crawlspace]">
                                        @foreach(__('woningdossier.cooperation.option') as $i => $option)
                                            <option @if(old('building_elements.extra.has_crawlspace', Hoomdossier::getMostCredibleValueFromCollection($buildingElementsOrderedOnInputSourceCredibility, 'extra.has_crawlspace')) == $i) selected="selected"
                                                    @endif value="{{ $i }}">
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endcomponent
                            @endcomponent

                            <div id="crawlspace-unknown-error" style="display: none;">
                                @component('cooperation.frontend.layouts.parts.alert', [
                                    'color' => 'yellow', 'dismissible' => false
                                ])
                                    <p class="text-yellow">
                                        @lang('floor-insulation.crawlspace.unknown-error.title')
                                    </p>
                                @endcomponent
                            </div>
                        </div>
                    </div>
                </div>
                <div id="crawlspace-wrapper" class="crawlspace-accessible">
                    <div class="flex flex-row flex-wrap w-full">
                        <div class="w-full md:w-1/2 md:pr-3">
                            <div id="has-crawlspace-access">
                                @component('cooperation.tool.components.step-question', [
                                    'id' => 'building_elements.extra.access',
                                    'translation' => 'floor-insulation.crawlspace-access', 'required' => false
                                ])
                                    @slot('sourceSlot')
                                        @include('cooperation.tool.components.source-list', [
                                            'inputType' => 'select',
                                            'inputValues' => __('woningdossier.cooperation.option'),
                                            'userInputValues' => $buildingElementsOrderedOnInputSourceCredibility,
                                            'userInputColumn' => 'extra.access'
                                        ])
                                    @endslot

                                    @component('cooperation.frontend.layouts.components.alpine-select')
                                        <select id="crawlspace_access" class="form-input"
                                                name="building_elements[extra][access]">
                                            @foreach(__('woningdossier.cooperation.option') as $i => $option)
                                                <option @if(old('building_elements.extra.access', Hoomdossier::getMostCredibleValueFromCollection($buildingElementsOrderedOnInputSourceCredibility, 'extra.access')) == $i) selected="selected"
                                                        @endif value="{{ $i }}">
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endcomponent
                                @endcomponent

                                <div id="crawlspace-no-access-error" style="display: none;">
                                    @component('cooperation.frontend.layouts.parts.alert', [
                                        'color' => 'yellow', 'dismissible' => false
                                    ])
                                        <p class="text-yellow">
                                            @lang('floor-insulation.crawlspace-access.no-access.title')
                                        </p>
                                    @endcomponent
                                </div>
                            </div>
                        </div>

                        <div class="w-full md:w-1/2 md:pl-3">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'building_elements.element_value_id',
                                'translation' => 'floor-insulation.crawlspace-height', 'required' => false
                            ])
                                @slot('sourceSlot')
                                    @include('cooperation.tool.components.source-list', [
                                        'inputType' => 'select', 'inputValues' => $crawlspace->values,
                                        'userInputValues' => $buildingElementsOrderedOnInputSourceCredibility,
                                        'userInputColumn' => 'element_value_id'
                                    ])
                                @endslot

                                @component('cooperation.frontend.layouts.components.alpine-select')
                                    <select id="crawlspace_height" class="form-input"
                                            name="building_elements[element_value_id]">
                                        @foreach($crawlspace->values as $crawlHeight)
                                            <option @if(old('crawlspace_height', Hoomdossier::getMostCredibleValueFromCollection($buildingElementsOrderedOnInputSourceCredibility, 'element_value_id')) == $crawlHeight->id) selected="selected"
                                                    @endif value="{{ $crawlHeight->id }}">
                                                {{ $crawlHeight->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endcomponent
                            @endcomponent
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap w-full crawlspace-accessible">
                        <div class="w-full sm:w-1/2 sm:pr-3">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'building_features.floor_surface', 'translation' => 'floor-insulation.surface',
                                'required' => true
                            ])
                                @slot('sourceSlot')
                                    @include('cooperation.tool.components.source-list', [
                                        'inputType' => 'input',
                                        'userInputValues' => $buildingFeaturesOrderedOnInputSourceCredibility,
                                        'userInputColumn' => 'floor_surface', 'needsFormat' => true
                                    ])
                                @endslot

                                <span class="input-group-prepend">@lang('general.unit.square-meters.title')</span>
                                <input id="floor_surface" type="text" name="building_features[floor_surface]" required="required"
                                       value="{{ \App\Helpers\NumberFormatter::format(old('building_features.floor_surface', Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnInputSourceCredibility, 'floor_surface')),1) }}"
                                       class="form-input">
                            @endcomponent
                        </div>
                        <div class="w-full sm:w-1/2 sm:pl-3">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'building_features.insulation_surface',
                                'translation' => 'floor-insulation.insulation-surface', 'required' => true
                            ])
                                @slot('sourceSlot')
                                    @include('cooperation.tool.components.source-list', [
                                        'inputType' => 'input',
                                        'userInputValues' => $buildingFeaturesOrderedOnInputSourceCredibility,
                                        'userInputColumn' => 'insulation_surface', 'needsFormat' => true
                                    ])
                                @endslot
                                <span class="input-group-prepend">@lang('general.unit.square-meters.title')</span>
                                <input id="insulation_floor_surface" type="text" required="required"
                                       name="building_features[insulation_surface]"
                                       value="{{ \App\Helpers\NumberFormatter::format(old('building_features.insulation_surface', Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnInputSourceCredibility, 'insulation_surface')),1) }}"
                                       class="form-input">
                            @endcomponent
                        </div>
                    </div>
                </div>

                <div class="flex flex-row flex-wrap w-full crawlspace-accessible">
                    <div class="w-full md:w-8/12 md:ml-2/12">
                        @component('cooperation.frontend.layouts.parts.alert', [
                            'color' => 'blue-800', 'dismissible' => false,
                        ])
                            <p class="text-blue-800">
                                @lang('floor-insulation.insulation-advice.text.title')
                            </p>
                            <p class="text-blue-800" id="insulation-advice"></p>
                        @endcomponent
                    </div>
                </div>

                <div id="indication-for-costs" class="crawlspace-accessible">
                    <hr>
                    @include('cooperation.tool.includes.section-title', [
                        'translation' => 'floor-insulation.indication-for-costs',
                        'id' => 'indication-for-costs'
                    ])

                    <div id="costs" class="flex flex-row flex-wrap w-full sm:pad-x-6">
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.gas', [
                                'translation' => 'floor-insulation.index.costs.gas'
                            ])
                        </div>
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.co2', [
                                'translation' => 'floor-insulation.index.costs.co2'
                            ])
                        </div>
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.savings-in-euro',[
                                'translation' => 'floor-insulation.index.savings-in-euro'
                            ])
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.indicative-costs',[
                                'translation' => 'floor-insulation.index.indicative-costs'
                            ])
                        </div>
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.comparable-rent',[
                                'translation' => 'floor-insulation.index.comparable-rent'
                            ])
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-row flex-wrap w-full hidden" id="no-crawlspace-error">
                <div class="w-full">
                    @component('cooperation.frontend.layouts.parts.alert', [
                        'color' => 'red', 'dismissible' => false,
                    ])
                        <p class="text-red">
                            @lang('floor-insulation.has-crawlspace.no-crawlspace.title')
                        </p>
                    @endcomponent
                </div>
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
             'translation' => 'floor-insulation.index.specific-situation'
        ])

        @component('cooperation.tool.components.panel', [
            'label' => __('default.buttons.download'),
        ])
            <ol>
                <li><a download=""
                       href="{{asset('storage/hoomdossier-assets/Maatregelblad_Vloerisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Vloerisolatie.pdf')))))}}</a>
                </li>
                <li><a download=""
                       href="{{asset('storage/hoomdossier-assets/Maatregelblad_Bodemisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Bodemisolatie.pdf')))))}}</a>
                </li>

            </ol>
        @endcomponent
    </form>
@endsection

@push('js')
    <script>

        $(document).ready(function () {


            $("select, input[type=radio], input[type=text]").change(() => formChange());

            function formChange() {

                var formData = $('#floor-insulation-form').serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.floor-insulation.calculate', compact('cooperation')) }}',
                    data: formData,
                    success: function (data) {

                        if (data.insulation_advice) {
                            $("#insulation-advice").html("<strong>" + data.insulation_advice + "</strong>");
                        } else {
                            $("#insulation-advice").html("");
                        }
                        if (data.hasOwnProperty('savings_gas')) {
                            $("input#savings_gas").val(hoomdossierRound(data.savings_gas));
                        }
                        if (data.hasOwnProperty('savings_co2')) {
                            $("input#savings_co2").val(hoomdossierRound(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')) {
                            $("input#savings_money").val(hoomdossierRound(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')) {
                            $("input#cost_indication").val(hoomdossierRound(data.cost_indication));
                        }
                        if (data.hasOwnProperty('interest_comparable')) {
                            $("input#interest_comparable").val(hoomdossierNumberFormat(data.interest_comparable, '{{ app()->getLocale() }}', 1));
                        }
                        if (data.hasOwnProperty('crawlspace_access')) {
                            $("#crawlspace-no-access-error").show();
                        } else {
                            $("#crawlspace-no-access-error").hide();
                        }
                        if (data.hasOwnProperty('crawlspace')) {
                            $("#crawlspace-unknown-error").show();
                        } else {
                            $("#crawlspace-unknown-error").hide();
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif

                        if ($("#floor-insulation-options select option:selected").data('calculate-value') === 6) {
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

            function resetDisplays() {
                $("#hideable").show();
                $("#answers").show();
                $("#has-no-crawlspace").show();
                console.log('reset');
            }

            function crawlspaceOptions() {
                if ($("#has_crawlspace").val() === "no") {
                    $(".crawlspace-accessible").hide();
                    $("#no-crawlspace-error").show();
                } else {
                    $(".crawlspace-accessible").show();
                    $("#no-crawlspace-error").hide();
                }
            }

            function checkInterestAndCurrentInsulation() {
                var interestedCalculateValue = $('#interest_element_{{$floorInsulation->id}} option:selected').data('calculate-value');
                var elementCalculateValue = $('#element_{{$floorInsulation->id}} option:selected').data('calculate-value');

                if (elementCalculateValue === 6) {
                    // nvt
                    $(".crawlspace-accessible").hide();
                    $("#has-no-crawlspace").hide();
                    $("#no-crawlspace-error").hide();
                    $('#floor-insulation-info-alert').find('.alert').hide()
                } else {
                    if ((elementCalculateValue === 3 || elementCalculateValue === 4 || elementCalculateValue === 5)/* && interestedCalculateValue <= 2*/) {
                        // insulation already present and there's interest
                        $('#hideable').hide();
                        $('#floor-insulation-info-alert').find('.alert').show();
                    } else {
                        $('#hideable').show();
                        //$("#has-no-crawlspace").show();
                        //crawlspaceOptions();
                        $('#floor-insulation-info-alert').find('.alert').hide();
                    }
                }
            }

            formChange();
        });

        $('#floor_surface').on('change', function () {
            if ($('#insulation_floor_surface').val().length == 0 || $('#insulation_floor_surface').val() == "0,0" || $('#insulation_floor_surface').val() == "0.00") {
                $('#insulation_floor_surface').val($('#floor_surface').val())
            }
        });

    </script>
@endpush

