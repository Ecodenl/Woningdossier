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

        <?php /** @var \App\Models\ServiceValue $howValues */ ?>

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
                    ?>

                    @component('cooperation.tool.components.step-question', ['id' => 'how', 'translation' => 'ventilation.how', 'required' => true])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $howValues, 'userInputValues' => null ,'userInputColumn' => 'how'])

                            @foreach($howValues as $howKey => $howValue)
                                    <div class="row" style="margin-left:5px;"><div class="col-sm-12">
                                            <label class="checkbox" style="font-weight: normal;">
                                    <input type="checkbox"
                                           name="building_ventilations[how][]"
                                           value="{{ $howKey }}"
                                           @if(empty(old()) &&
                                            in_array($howKey, old('building_ventilations.how',[ \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations()->where('how', $howKey), 'how', null, \App\Helpers\Hoomdossier::getMostCredibleInputSource($building->buildingVentilations())) ])))
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
                    ?>

                    @component('cooperation.tool.components.step-question', ['id' => 'living_situation', 'translation' => 'ventilation.living_situation', 'required' => true])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $livingSituationValues, 'userInputValues' => null ,'userInputColumn' => 'living_situation'])
                            @foreach($livingSituationValues as $lsKey => $lsValue)

                                    <div class="row" style="margin-left:5px;"><div class="col-sm-12">
                                            <label class="checkbox" style="font-weight: normal;">
                                    <input type="checkbox"
                                           name="building_ventilations[living_situation][]"
                                           value="{{ $lsKey }}"
                                           @if(empty(old()) &&
                                            in_array($lsKey, old('building_ventilations.living_situation',[ \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations()->where('living_situation',$lsKey), 'living_situation', null, \App\Helpers\Hoomdossier::getMostCredibleInputSource($building->buildingVentilations())) ])))
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
                    ?>

                    @component('cooperation.tool.components.step-question', ['id' => 'usage', 'translation' => 'ventilation.usage', 'required' => true])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $usageValues, 'userInputValues' => null ,'userInputColumn' => 'usage'])
                            @foreach($usageValues as $uKey => $uValue)

                                <div class="row" style="margin-left:5px;"><div class="col-sm-12">
                                <label class="checkbox" style="font-weight: normal;">
                                    <input type="checkbox"
                                           name="building_ventilations[usage][]"
                                           value="{{ $uKey }}"
                                           @if(empty(old()) &&
                                            in_array($uKey, old('building_ventilations.usage',[ \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingVentilations()->where('usage',$uKey), 'usage', null, \App\Helpers\Hoomdossier::getMostCredibleInputSource($building->buildingVentilations())) ])))
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

    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $("input[type=checkbox]").change(function(){

                // only do this when building_ventilations[how][] 'none' is clicked to be enabled!
                // unset all other values for building_ventilations[how][]
                // using .indexOf instead of .includes because of Internet Exploder compatibility
                if (this.value === 'none' && this.checked && this.name.indexOf('[how]') !== -1){
                    $(this).parent().parent().parent().parent().find("input[type=checkbox]").not(this).prop('checked', false);
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

            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');

        });


    </script>
@endpush