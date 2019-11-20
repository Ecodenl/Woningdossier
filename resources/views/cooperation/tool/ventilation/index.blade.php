@extends('cooperation.tool.layout')

@section('step_title', "Ventilatie: " . $buildingVentilation->value)

@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.ventilation.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}

        <div class="row">
            <div class="col-sm-12">
                <p style="margin-left: -5px">
                    U heeft aangegeven dat er in de woning natuurlijke
                    ventilatie aanwezig is. In een huis met natuurlijke
                    ventilatie zit geen mechanisch ventilatiesysteem, dat
                    betekent dat er alleen via natuurlijke weg geventileerd kan
                    worden door ventilatieroosters en bijvoorbeeld ramen of
                    deuren.
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
                    $howValues = [
                        'windows-doors' => 'Ventilatieroosters in ramen / deuren',
                        'other'         => 'Ventilatieroosters overig',
                        'windows'       => '(Klep)ramen',
                        'none'          => 'Geen ventilatievoorzieningen',
                    ];
                    /** @var \Illuminate\Support\Collection $ventilations */
                    $ventilations = collect([]);
                    foreach($howValues as $uvalue => $uname){
                        $ventilation = new \App\Models\BuildingVentilation();
                        $ventilation->value = $uname;
                        $ventilation->how = $uvalue;
                        $ventilations->push($ventilation);
                    }
                    ?>

                    @component('cooperation.tool.components.step-question', ['id' => 'how', 'translation' => 'cooperation/tool/ventilation.index.how', 'required' => true])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $ventilations, 'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'how'])

                            @foreach($howValues as $howKey => $howValue)
                                    <div class="row" style="margin-left:5px;"><div class="col-sm-12">
                                            <label class="checkbox" style="font-weight: normal;">
                                    <input type="checkbox"
                                           name="building_ventilations[how][]"
                                           value="{{ $howKey }}"
                                           @if(in_array($howKey, old('building_ventilations.how', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'how', []))))
                                           checked="checked"
                                            @endif
                                    >
                                    {{ $howValue }}
                                            </label></div></div>
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
                                <strong>Er is op dit moment mogelijkerwijs onvoldoende ventilatie, het kan zinvol zijn om dit door een specialist te laten beoordelen.</strong>
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
                    $livingSituationValues = [
                        'dry-laundry'       => 'Ik droog de was in huis',
                        'fireplace'         => 'Ik stook een open haard of houtkachel',
                        'combustion-device' => 'Ik heb een open verbrandingstoestel',
                        'moisture'          => 'Ik heb last van schimmel op de muren',
                    ];
                    /** @var \Illuminate\Support\Collection $ventilations */
                    $ventilations = collect([]);
                    foreach($livingSituationValues as $uvalue => $uname){
                        $ventilation = new \App\Models\BuildingVentilation();
                        $ventilation->value = $uname;
                        $ventilation->living_situation = $uvalue;
                        $ventilations->push($ventilation);
                    }
                    ?>

                    @component('cooperation.tool.components.step-question', ['id' => 'living_situation', 'translation' => 'cooperation/tool/ventilation.index.living-situation', 'required' => true])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $ventilations, 'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'living_situation'])
                            @foreach($livingSituationValues as $lsKey => $lsValue)

                                    <?php
                                    Log::debug(

                                        is_array(old('building_ventilations.living_situation',\App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'living_situation',[])))
                                            ? 'de living_situation van building ventilations is wel een array'
                                            : 'de living_situation van building ventilations is geen array'
                                    )
                                    ?>
                                    <div class="row" style="margin-left:5px;"><div class="col-sm-12">
                                            <label class="checkbox" style="font-weight: normal;">
                                    <input type="checkbox"
                                           name="building_ventilations[living_situation][]"
                                           value="{{ $lsKey }}"
                                           @if(in_array($lsKey, old('building_ventilations.living_situation',\App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'living_situation',[]))))
                                           checked="checked"
                                            @endif
                                    >
                                    {{ $lsValue }}
                                </label>
                                        </div></div>

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
                                <li class="dry-laundry" style="display: none;">Ventileer extra als de was te drogen hangt, door de schakelaar op de hoogste stand te zetten of een raam open te doen. Hang de was zoveel mogelijk buiten te drogen.</li>
                                <li class="fireplace" style="display: none;">Zorg voor extra ventilatie tijdens het stoken van open haard of houtkachel, zowel voor de aanvoer van zuurstof als de afvoer van schadelijke stoffen. Zet bijvoorbeeld een (klep)raam open.</li>
                                <li class="combustion-device" style="display: none;">Zorg bij een open verbrandingstoestel in ieder geval dat er altijd voldoende luchttoevoer is. Anders kan onvolledige verbranding optreden waarbij het gevaarlijke koolmonoxide kan ontstaan.</li>
                                <li class="moisture" style="display: none;">Wanneer u last heeft van schimmel of vocht in huis dan wordt geadviseerd om dit door een specialist te laten beoordelen.</li>
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
                    $usageValues = [
                        'sometimes-off'      => 'Ik zet de ventilatie unit wel eens helemaal uit',
                        'no-maintenance'     => 'Ik doe geen onderhoud op de ventilatie unit',
                        'filter-replacement' => 'Het filter wordt niet of onregelmatig vervangen',
                        'closed'             => 'Ik heb de roosters / klepramen voor aanvoer van buitenlucht vaak dicht staan',
                    ];
                    /** @var \Illuminate\Support\Collection $ventilations */
                    $ventilations = collect([]);
                    foreach($usageValues as $uvalue => $uname){
                        $ventilation = new \App\Models\BuildingVentilation();
                        $ventilation->value = $uname;
                        $ventilation->usage = $uvalue;
                        $ventilations->push($ventilation);
                    }
                    ?>

                    @component('cooperation.tool.components.step-question', ['id' => 'usage', 'translation' => 'cooperation/tool/ventilation.index.usage', 'required' => true])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $ventilations, 'userInputValues' => $myBuildingVentilations ,'userInputColumn' => 'usage'])
                            @foreach($usageValues as $uKey => $uValue)

                                <div class="row" style="margin-left:5px;"><div class="col-sm-12">
                                <label class="checkbox" style="font-weight: normal;">
                                    <input type="checkbox"
                                           name="building_ventilations[usage][]"
                                           value="{{ $uKey }}"
                                           @if(in_array($uKey, old('building_ventilations.usage', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations(), 'usage',[]))))
                                            checked="checked"
                                            @endif
                                    >
                                    {{ $uValue }}
                                </label>
                                    </div></div>

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
                                <li class="sometimes-off" style="display: none;">Laat de ventilatie unit altijd aan staan, anders wordt er helemaal niet geventileerd en hoopt vocht en vieze lucht zich op. Trek alleen bij onderhoud of in geval van een ramp (als de overheid adviseert ramen en deuren te sluiten) de stekker van de ventilatie-unit uit het stopcontact.</li>
                                <li class="no-maintenance" style="display: none;">Laat iedere 2 jaar een onderhoudsmonteur langskomen, regelmatig onderhoud van de ventilatie-unit is belangrijk. Kijk in de gebruiksaanwijzing hoe vaak onderhoud aan de unit nodig is.</li>
                                <li class="filter-replacement" style="display: none;">Voor een goede luchtkwaliteit is het belangrijk om regelmatig de filter te vervangen. Kijk in de gebruiksaanwijzing hoe vaak de filters vervangen moeten worden.</li>
                                <li class="closed" style="display: none;">Zorg dat de roosters in de woonkamer en slaapkamers altijd open staan. Schone lucht in huis is noodzakelijk voor je gezondheid.</li>
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
           'translation' => 'cooperation/tool/ventilation.index.comment'
        ])

    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $("input[type=checkbox]").change(function(){

                if(this.name.indexOf('[how]') !== -1) {
                    // only do this when building_ventilations[how][] 'none' is clicked to be enabled!
                    // unset all other values for building_ventilations[how][]
                    // using .indexOf instead of .includes because of Internet Exploder compatibility
                    if (this.value === 'none' && this.checked) {
                        $(this).parent().parent().parent().parent().find("input[type=checkbox]").not(this).prop('checked', false);
                    }
                    else {
                        $("input[type=checkbox][value=none]").prop('checked', false);
                    }
                }

                checkAlerts();

                formChange($(this));
            });

            function checkAlerts(element){

                // how
                // hide by default
                $('#how-none-alert').hide();

                $('input[name^="building_ventilations[how]"]').filter(':visible').map(function() {
                    if (this.value === 'none' && this.checked && this.name.indexOf('[how]') !== -1) {
                        // Show the alert
                        $('#how-none-alert').show();
                    }
                });

                // living_situation
                $('#living_situation-alert').hide();
                $('input[name^="building_ventilations[living_situation]"]').filter(':visible').map(function() {
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
                $('input[name^="building_ventilations[usage]"]').filter(':visible').map(function() {
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

                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.ventilation.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {
                        if (data.hasOwnProperty('improvement')){
                            $("p#improvement").html(data.improvement);
                        }

                        if (data.hasOwnProperty('advices')){
                            var advices = $(".advices");
                            advices.html('<div class="col-sm-9"><strong>Verbetering</strong></div><div class="col-sm-3"><strong>Interesse</strong></div>');
                            $.each(data.advices, function(i, element){
                                var checked = '';
                                if (element.hasOwnProperty('interest') && element.interest === true) {
                                    checked = ' checked="checked"';
                                }
                                advices.append('<div class="col-sm-9">' + element.name + '</div><div class="col-sm-3"><input type="checkbox" name="user_interests[]" value="' + element.id + '"' + checked +'></div>');
                            });
                        }

                        //if (data.hasOwnProperty('remark')){
                        //    $("p#remark").html(data.remark);
                        //}

                        if (data.hasOwnProperty('result') && data.result.hasOwnProperty('crack_sealing')) {

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