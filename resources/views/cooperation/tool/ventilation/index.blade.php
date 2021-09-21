@extends('cooperation.frontend.layouts.tool')

@section('step_title', "Ventilatie: " . $buildingVentilation->value)

@section('content')

    <form method="POST" id="ventilation-form"
          action="{{ route('cooperation.tool.ventilation.store', compact('cooperation')) }}" autocomplete="off">
        @csrf

        <div class="flex flex-row flex-wrap w-full">
            <div class="w-full">
                <p style="margin-left: -5px">
                    @lang('cooperation/tool/ventilation.index.intro.'.\Illuminate\Support\Str::slug($buildingVentilation->value))
                </p>
            </div>
        </div>

        <?php
        $myBuildingVentilations = $building->buildingVentilations()->forMe()->get();
        /** @var \App\Models\ServiceValue $howValues */
        ?>

        @if(in_array($buildingVentilation->calculate_value, [1,2,]))
        <!-- how : natural & mechanic -->
            <div class="flex flex-row flex-wrap w-full natural mechanic">
                <div class="w-full">

                    <?php
                    $ventilations = collect();
                    foreach ($howValues as $uvalue => $uname) {
                        $ventilation = new \App\Models\BuildingVentilation();
                        $ventilation->value = $uname;
                        $ventilation->how = $uvalue;
                        $ventilations->push($ventilation);
                    }
                    ?>

                    @component('cooperation.tool.components.step-question', [
                        'id' => 'how', 'name' => 'building_ventilations.how',
                        'translation' => 'cooperation/tool/ventilation.index.how', 'required' => true,
                        'inputGroupClass' => 'pad-x-3',
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'checkbox', 'inputValues' => $ventilations,
                                'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'how'
                            ])
                        @endslot

                        @foreach($howValues as $howKey => $howValue)
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="building-ventilation-how-{{$howKey}}" name="building_ventilations[how][]"
                                       value="{{ $howKey }}"
                                       @if(in_array($howKey, old('building_ventilations.how', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'how', []) ?? []))) checked="checked" @endif>
                                <label for="building-ventilation-how-{{$howKey}}">
                                    <span class="checkmark"></span>
                                    <span>{{ $howValue }}</span>
                                </label>
                            </div>
                        @endforeach
                    @endcomponent
                </div>
            </div>

            <div class="how-none-warning">
                <div class="flex flex-row flex-wrap w-full" id="how-none-alert" style="display: none;">
                    <div class="w-full md:w-8/12 md:ml-2/12">
                        @component('cooperation.frontend.layouts.parts.alert', [
                            'color' => 'yellow',
                            'dismissible' => false,
                        ])
                            <p class="text-yellow">
                                <strong>
                                    Er is op dit moment mogelijkerwijs onvoldoende ventilatie, het kan zinvol zijn
                                    om dit door een specialist te laten beoordelen.
                                </strong>
                            </p>
                        @endcomponent
                    </div>
                </div>
            </div>

        @endif


    <!-- living_situation: natural, mechanic, balanced, decentral -->
        @if(in_array($buildingVentilation->calculate_value, [1,2,3,4,]))
        <!-- living_situation: natural, mechanic, balanced, decentral -->
            <div class="flex flex-row flex-wrap w-full natural mechanic balanced decentral">
                <div class="w-full">

                    <?php
                    /** @var \Illuminate\Support\Collection $ventilations */
                    $ventilations = collect([]);
                    foreach ($livingSituationValues as $uvalue => $uname) {
                        $ventilation = new \App\Models\BuildingVentilation();
                        $ventilation->value = $uname;
                        $ventilation->living_situation = $uvalue;
                        $ventilations->push($ventilation);
                    }
                    ?>

                    @component('cooperation.tool.components.step-question', [
                        'id' => 'living_situation',
                        'translation' => 'cooperation/tool/ventilation.index.living-situation',
                        'inputGroupClass' => 'pad-x-3'
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'checkbox', 'inputValues' => $ventilations,
                                'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'living_situation'
                            ])
                        @endslot
                            @foreach($livingSituationValues as $lsKey => $lsValue)

                                <?php
                                Log::debug(

                                    is_array(old('building_ventilations.living_situation', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'living_situation', []) ?? []))
                                        ? 'de living_situation van building ventilations is wel een array'
                                        : 'de living_situation van building ventilations is geen array'
                                )
                                ?>
                                <div class="checkbox-wrapper">
                                    <?php
                                    // default wont work, null will be returned anyways.
                                    $mostCredibleLivingSituation = \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'living_situation') ?? [];
                                    ?>
                                    <input type="checkbox" id="building-ventilation-living-situation-{{$lsKey}}" name="building_ventilations[living_situation][]"
                                           value="{{ $lsKey }}"
                                           @if(in_array($lsKey, old('building_ventilations.living_situation', $mostCredibleLivingSituation))) checked="checked" @endif>
                                    <label for="building-ventilation-living-situation-{{$lsKey}}">
                                        <span class="checkmark"></span>
                                        <span>{{ $lsValue }}</span>
                                    </label>
                                </div>
                            @endforeach
                    @endcomponent
                </div>
            </div>

            <div class="living_situation-warning">
                <div class="flex flex-row flex-wrap w-full" id="living_situation-alert" style="display: none;">
                    <div class="w-full md:w-8/12 md:ml-2/12">
                        @component('cooperation.frontend.layouts.parts.alert', [
                            'color' => 'yellow', 'dismissible' => false,
                        ])
                            <ul>
                                @foreach(__('cooperation/tool/ventilation.index.living-situation-warnings') as $livingSituationWarningType => $livingSituationWarningTranslation)
                                <li class="{{$livingSituationWarningType}}" style="display: none;">
                                    {!! $livingSituationWarningTranslation !!}
                                </li>
                                @endforeach
                            </ul>
                        @endcomponent
                    </div>
                </div>
            </div>
        @endif

    <!-- living_situation: mechanic, balanced, decentral -->
        @if(in_array($buildingVentilation->calculate_value, [2,3,4,]))
        <!-- living_situation: natural, mechanic, balanced, decentral -->
            <div class="flex flex-row flex-wrap w-full mechanic balanced decentral">
                <div class="w-full">

                    <?php
                    /** @var \Illuminate\Support\Collection $ventilations */
                    $ventilations = collect([]);
                    foreach ($usageValues as $uvalue => $uname) {
                        $ventilation = new \App\Models\BuildingVentilation();
                        $ventilation->value = $uname;
                        $ventilation->usage = $uvalue;
                        $ventilations->push($ventilation);
                    }
                    ?>

                    @component('cooperation.tool.components.step-question', [
                        'id' => 'usage', 'translation' => 'cooperation/tool/ventilation.index.usage',
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'checkbox', 'inputValues' => $ventilations,
                                'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'usage'
                            ])
                        @endslot

                        @foreach($usageValues as $uKey => $uValue)
                            <div class="checkbox-wrapper pr-3">
                                <input type="checkbox" id="building-ventilation-usage-{{$uKey}}" name="building_ventilations[usage][]"
                                       value="{{ $uKey }}"
                                       @if(in_array($uKey, old('building_ventilations.usage', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'usage',[]) ?? [])))  checked="checked" @endif>
                                <label for="building-ventilation-usage-{{$uKey}}">
                                    <span class="checkmark"></span>
                                    <span>{{ $uValue }}</span>
                                </label>
                            </div>
                        @endforeach
                    @endcomponent
                </div>
            </div>

            <div class="usage-warning">
                <div class="flex flex-row flex-wrap w-full" id="usage-alert" style="display: none;">
                    <div class="w-full md:w-8/12 md:ml-2/12">
                        @component('cooperation.frontend.layouts.parts.alert', [
                            'color' => 'yellow', 'dismissible' => false,
                        ])
                            <ul>
                                @foreach(__('cooperation/tool/ventilation.index.usage-warnings') as $usageWarningType => $usageWarningTranslation)
                                    <li class="{{$usageWarningType}}" style="display: none;">
                                        {!! $usageWarningTranslation !!}
                                    </li>
                                @endforeach
                            </ul>
                        @endcomponent
                    </div>
                </div>
            </div>
        @endif

        <div class="flex flex-row flex-wrap w-full mt-4">
            <div class="w-full">
                <h3 class="heading-3">Verbeteropties</h3>
            </div>
        </div>
        <div class="flex flex-row flex-wrap w-full mb-4">
            <div class="w-full">
                <p id="improvement"></p>
            </div>
        </div>
        <div class="flex flex-row flex-wrap w-full my-4 advices">

        </div>
        <div class="flex flex-row flex-wrap w-full my-4">
            <div class="w-full">
                <p id="remark"></p>
            </div>
        </div>

        <div id="indication-for-costs">
            <hr>
            @include('cooperation.tool.includes.section-title', [
                'translation' => 'cooperation/tool/ventilation.index.indication-for-costs',
                'id' => 'indication-for-costs'
            ])

            <div id="costs" class="flex flex-row flex-wrap w-full sm:pad-x-6">
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.gas', [
                        'translation' => 'cooperation/tool/ventilation.index.costs.gas'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.co2', [
                        'translation' => 'cooperation/tool/ventilation.index.costs.co2'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro', [
                        'translation' => 'cooperation/tool/ventilation.index.savings-in-euro'
                    ])
                </div>
            </div>
            <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs', [
                        'translation' => 'cooperation/tool/ventilation.index.indicative-costs'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent', [
                        'translation' => 'cooperation/tool/ventilation.index.comparable-rent'
                    ])
                </div>
            </div>
        </div>

        <div id="costs-other" class="mt-4">
            @include('cooperation.tool.includes.section-title', [
                'translation' => 'cooperation/tool/ventilation.index.indication-for-costs-other',
                'id' => 'indication-for-costs'
            ])

            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full">
                    {!! __('cooperation/tool/ventilation.index.indication-for-costs-other.text') !!}
                </div>
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
           'translation' => 'cooperation/tool/ventilation.index.specific-situation'
        ])

        @component('cooperation.tool.components.panel', [
            'label' => __('default.buttons.download')
        ])
            <ol>
                @foreach(['Maatregelblad_Ventilatiebox.pdf', 'Maatregelblad_Kierdichting_II.pdf'] as $fileName)
                    <?php
                    $link = "storage/hoomdossier-assets/{$fileName}"
                    ?>
                    <li>
                        <a href="{{asset($link)}}" download="">
                            {{ucfirst(strtolower($fileName))}}
                        </a>
                    </li>
                @endforeach
            </ol>
        @endcomponent
    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $("input[type=checkbox]").change(function (event) {
                // when trigger('') is used, there wont be a orginalEvent,
                var eventIsTriggeredByUser = typeof event.originalEvent !== "undefined";

                // we only want to do this when the event is triggerd by a user and not jquery itself.
                // if this gets triggered onload the none value will always be unchecked.
                if (this.name.indexOf('[how]') !== -1 && eventIsTriggeredByUser) {
                    // only do this when building_ventilations[how][] 'none' is clicked to be enabled!
                    // unset all other values for building_ventilations[how][]
                    // using .indexOf instead of .includes because of Internet Exploder compatibility
                    if (this.value === 'none' && this.checked) {
                        $(this).parents('.input-group').first().find("input[type=checkbox]").not(this).prop('checked', false);
                    } else {
                        $("input[type=checkbox][value=none]").prop('checked', false);
                    }
                }

                checkAlerts();

                formChange($(this));
            });

            function checkAlerts(element) {

                // how
                // hide by default
                $('#how-none-alert').hide();

                $('input[name^="building_ventilations[how]"]').filter(':enabled').map(function () {
                    if (this.value === 'none' && this.checked && this.name.indexOf('[how]') !== -1) {
                        // Show the alert
                        $('#how-none-alert').show();
                    }
                });

                // living_situation
                $('#living_situation-alert').hide();
                $('input[name^="building_ventilations[living_situation]"]').filter(':enabled').map(function () {
                    var selector = $("#living_situation-alert ul li." + this.value);
                    selector.hide();
                    if (this.checked) {
                        $('#living_situation-alert').show();
                        // Show the alert
                        selector.show();
                    }
                });

                // usage
                // living_situation
                $('#usage-alert').hide();
                $('input[name^="building_ventilations[usage]"]').filter(':enabled').map(function () {
                    var selector = $("#usage-alert ul li." + this.value);
                    selector.hide();
                    if (this.checked) {
                        $('#usage-alert').show();
                        // Show the alert
                        selector.show();
                    }
                });
            }

            function formChange(element) {
                var form = $('#ventilation-form').serialize();
                var indicationForCosts = $('#indication-for-costs');
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.ventilation.calculate', compact('cooperation')) }}',
                    data: form,
                    success: function (data) {
                        if (data.hasOwnProperty('improvement')) {
                            $("p#improvement").html(data.improvement);
                        }

                        console.log(data.considerables);
                        if (data.hasOwnProperty('considerables') && data.considerables.length !== 0) {
                            var advices = $(".advices");
                            advices.html('<div class="w-full sm:w-3/4"><strong>Verbetering</strong></div><div class="w-full sm:w-1/4"><strong>Interesse</strong></div>');
                            $.each(data.considerables, function (i, considerable) {
                                var checked = '';
                                if (considerable.hasOwnProperty('is_considerable') && considerable.is_considerable == true) {
                                    checked = ' checked="checked"';
                                }
                                advices.append('<div class="w-full sm:w-3/4">' + considerable.name + '</div><div class="w-full sm:w-1/4"><input type="checkbox" name="considerables['+element.id+'][is_considering]" value="1" '+checked+'></div>');
                            });
                            indicationForCosts.show();
                        } else {
                            indicationForCosts.hide();
                        }

                        //if (data.hasOwnProperty('remark')){
                        //    $("p#remark").html(data.remark);
                        //}

                        // when the costs indication is empty, we can safley asume there are no cost indications.
                        if (data.hasOwnProperty('result') && data.result.hasOwnProperty('crack_sealing') && data.result.crack_sealing.cost_indication !== null) {
                            indicationForCosts.show();
                            if (data.result.crack_sealing.hasOwnProperty('savings_gas')) {
                                $("input#savings_gas").val(hoomdossierRound(data.result.crack_sealing.savings_gas));
                            }
                            if (data.result.crack_sealing.hasOwnProperty('savings_co2')) {
                                $("input#savings_co2").val(hoomdossierRound(data.result.crack_sealing.savings_co2));
                            }
                            if (data.result.crack_sealing.hasOwnProperty('savings_money')) {
                                $("input#savings_money").val(hoomdossierRound(data.result.crack_sealing.savings_money));
                            }
                            if (data.result.crack_sealing.hasOwnProperty('cost_indication')) {
                                $("input#cost_indication").val(hoomdossierRound(data.result.crack_sealing.cost_indication));
                            }
                            if (data.result.crack_sealing.hasOwnProperty('interest_comparable')) {
                                $("input#interest_comparable").val(hoomdossierNumberFormat(data.result.crack_sealing.interest_comparable, '{{ app()->getLocale() }}', 1));
                            }
                        } else {
                            indicationForCosts.hide();
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            }

            $('input[type="checkbox"]:enabled').first().trigger('change');
        });
    </script>
@endpush