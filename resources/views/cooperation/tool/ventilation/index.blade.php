@extends('cooperation.tool.layout')

@section('step_title', "Ventilatie: " . $buildingVentilation->value)

@section('step_content')
    <form method="POST" action="{{ route('cooperation.tool.ventilation.store', ['cooperation' => $cooperation]) }}" autocomplete="off">
        {{ csrf_field() }}

        <div class="row">
            <div class="col-sm-12">
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
            <div class="row natural mechanic">
                <div class="col-sm-12">

                    <?php
                    $ventilations = collect();
                    foreach ($howValues as $uvalue => $uname) {
                        $ventilation = new \App\Models\BuildingVentilation();
                        $ventilation->value = $uname;
                        $ventilation->how = $uvalue;
                        $ventilations->push($ventilation);
                    }
                    ?>

                    @component('cooperation.tool.components.step-question', ['id' => 'how', 'name' => 'building_ventilations.how', 'translation' => 'cooperation/tool/ventilation.index.how', 'required' => true])
                        @component('cooperation.tool.components.input-group',
                  ['inputType' => 'checkbox', 'inputValues' => $ventilations, 'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'how'])

                            @foreach($howValues as $howKey => $howValue)
                                <div class="row" style="margin-left:5px;">
                                    <div class="col-sm-12">
                                        <label class="checkbox" style="font-weight: normal;">
                                            <input type="checkbox"
                                                   name="building_ventilations[how][]"
                                                   value="{{ $howKey }}"
                                                   @if(in_array($howKey, old('building_ventilations.how', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'how', []) ?? [])))
                                                   checked="checked"
                                                    @endif
                                            >
                                            {{ $howValue }}
                                        </label></div>
                                </div>
                            @endforeach

                        @endcomponent
                    @endcomponent

                </div>
            </div>

            <div class="how-none-warning">
                <div class="row" id="how-none-alert" style="display: none;">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-warning" role="alert">
                            <p>
                                <strong>Er is op dit moment mogelijkerwijs onvoldoende ventilatie, het kan zinvol zijn
                                    om dit door een specialist te laten beoordelen.</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        @endif


    <!-- living_situation: natural, mechanic, balanced, decentral -->
        @if(in_array($buildingVentilation->calculate_value, [1,2,3,4,]))
        <!-- living_situation: natural, mechanic, balanced, decentral -->
            <div class="row natural mechanic balanced decentral">
                <div class="col-sm-12">

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

                    @component('cooperation.tool.components.step-question', ['id' => 'living_situation', 'translation' => 'cooperation/tool/ventilation.index.living-situation'])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $ventilations, 'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'living_situation'])
                            @foreach($livingSituationValues as $lsKey => $lsValue)

                                <?php
                                Log::debug(

                                    is_array(old('building_ventilations.living_situation', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'living_situation', []) ?? []))
                                        ? 'de living_situation van building ventilations is wel een array'
                                        : 'de living_situation van building ventilations is geen array'
                                )
                                ?>
                                <div class="row" style="margin-left:5px;">
                                    <div class="col-sm-12">
                                        <label class="checkbox" style="font-weight: normal;">
                                            <input type="checkbox"
                                                   name="building_ventilations[living_situation][]"
                                                   value="{{ $lsKey }}"
                                                   <?php
                                                   // default wont work, null will be returned anyways.
                                                   $mostCredibleLivingSituation = \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'living_situation') ?? [];
                                                   ?>
                                                   @if(in_array($lsKey, old('building_ventilations.living_situation', $mostCredibleLivingSituation)))
                                                   checked="checked"
                                                    @endif
                                            >
                                            {{ $lsValue }}
                                        </label>
                                    </div>
                                </div>

                            @endforeach
                        @endcomponent
                    @endcomponent

                </div>
            </div>

            <div class="living_situation-warning">
                <div class="row" id="living_situation-alert" style="display: none;">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-warning" role="alert">
                            <ul>
                                @foreach(__('cooperation/tool/ventilation.index.living-situation-warnings') as $livingSituationWarningType => $livingSituationWarningTranslation)
                                <li class="{{$livingSituationWarningType}}" style="display: none;">
                                    {!! $livingSituationWarningTranslation !!}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    <!-- living_situation: mechanic, balanced, decentral -->
        @if(in_array($buildingVentilation->calculate_value, [2,3,4,]))
        <!-- living_situation: natural, mechanic, balanced, decentral -->
            <div class="row mechanic balanced decentral">
                <div class="col-sm-12">

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

                    @component('cooperation.tool.components.step-question', ['id' => 'usage', 'translation' => 'cooperation/tool/ventilation.index.usage'])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $ventilations, 'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'usage'])
                            @foreach($usageValues as $uKey => $uValue)

                                <div class="row" style="margin-left:5px;">
                                    <div class="col-sm-12">
                                        <label class="checkbox" style="font-weight: normal;">
                                            <input type="checkbox"
                                                   name="building_ventilations[usage][]"
                                                   value="{{ $uKey }}"
                                                   @if(in_array($uKey, old('building_ventilations.usage', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'usage',[]) ?? [])))
                                                   checked="checked"
                                                    @endif
                                            >
                                            {{ $uValue }}
                                        </label>
                                    </div>
                                </div>

                            @endforeach
                        @endcomponent
                    @endcomponent

                </div>
            </div>

            <div class="usage-warning">
                <div class="row" id="usage-alert" style="display: none;">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-warning" role="alert">
                            <ul>
                                @foreach(__('cooperation/tool/ventilation.index.usage-warnings') as $usageWarningType => $usageWarningTranslation)
                                    <li class="{{$usageWarningType}}" style="display: none;">
                                        {!! $usageWarningTranslation !!}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <h3>Verbeteropties</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p id="improvement"></p>
            </div>
        </div>
        <div class="row advices">

        </div>
        <div class="row">
            <div class="col-sm-12">
                <p id="remark"></p>
            </div>
        </div>

        <div id="indication-for-costs">
            <hr>
            @include('cooperation.tool.includes.section-title', [
                'translation' => 'cooperation/tool/ventilation.index.indication-for-costs',
                'id' => 'indication-for-costs'
            ])

            <div id="costs" class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.gas', ['translation' => 'cooperation/tool/ventilation.index.costs.gas'])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.co2', ['translation' => 'cooperation/tool/ventilation.index.costs.co2'])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro', [
                        'translation' => 'cooperation/tool/ventilation.index.savings-in-euro'
                    ])
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs', [
                        'translation' => 'cooperation/tool/ventilation.index.indicative-costs'
                    ])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent', [
                        'translation' => 'cooperation/tool/ventilation.index.comparable-rent'
                    ])
                </div>
            </div>
        </div>

        <div id="costs-other">
            @include('cooperation.tool.includes.section-title', [
                'translation' => 'cooperation/tool/ventilation.index.indication-for-costs-other',
                'id' => 'indication-for-costs'
            ])

            <div class="row">
                <div class="col-sm-12">
                    {!! \App\Helpers\Translation::translate('cooperation/tool/ventilation.index.indication-for-costs-other.text') !!}
                </div>
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
           'translation' => 'cooperation/tool/ventilation.index.specific-situation'
        ])

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
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
                    </div>
                </div>
            </div>
        </div>

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
                        $(this).parent().parent().parent().parent().find("input[type=checkbox]").not(this).prop('checked', false);
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

                $('input[name^="building_ventilations[how]"]').filter(':visible').map(function () {
                    if (this.value === 'none' && this.checked && this.name.indexOf('[how]') !== -1) {
                        // Show the alert
                        $('#how-none-alert').show();
                    }
                });

                // living_situation
                $('#living_situation-alert').hide();
                $('input[name^="building_ventilations[living_situation]"]').filter(':visible').map(function () {
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
                $('input[name^="building_ventilations[usage]"]').filter(':visible').map(function () {
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
                var form = element.closest("form").serialize();
                var indicationForCosts = $('#indication-for-costs');
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.ventilation.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {
                        if (data.hasOwnProperty('improvement')) {
                            $("p#improvement").html(data.improvement);
                        }

                        if (data.hasOwnProperty('advices') && data.advices.length !== 0) {
                            var advices = $(".advices");
                            advices.html('<div class="col-sm-9"><strong>Verbetering</strong></div><div class="col-sm-3"><strong>Interesse</strong></div>');
                            $.each(data.advices, function (i, element) {
                                var checked = '';
                                if (element.hasOwnProperty('interest') && element.interest === true) {
                                    checked = ' checked="checked"';
                                }
                                advices.append('<div class="col-sm-9">' + element.name + '</div><div class="col-sm-3"><input type="checkbox" name="user_interests[]" value="' + element.id + '"' + checked + '></div>');
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

            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');

        });


    </script>
@endpush