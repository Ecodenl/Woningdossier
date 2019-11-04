@extends('cooperation.tool.layout')

{{--@section('step_title', \App\Helpers\Translation::translate('ventilation-information.title.title'))--}}
@section('step_title', 'Natuurlijke ventilatie')

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
                    ['inputType' => 'select', 'inputValues' => $howValues, 'userInputValues' => null ,'userInputColumn' => 'how'])
                            <select id="how" class="form-control" name="building_ventilations[how]">
                                @foreach($howValues as $howKey => $howValue)
                                    <option @if(old('how', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'how'))  == $howKey) selected="selected"
                                            @endif value="{{ $howKey }}">{{ $howValue }}</option>
                                @endforeach
                            </select>@endcomponent
                    @endcomponent

                </div>
            </div>
        @endif



        @if(in_array($buildingVentilation->calculate_value, [1,2,3,4,]))
        <!-- living_situation: natural, mechanic, balanced, decentral -->
            <div class="row natural mechanic">
                <div class="col-sm-12">

                    <?php
                    $livingSituationValues = [
                        'dry-laundry'       => 'Ik droog de was in huis',
                        'fireplace'         => 'Ik stook een open haard of houtkachel',
                        'combustion-device' => 'Ik heb een open verbrandingstoestel',
                        'moisture'          => 'Ik heb last van schimmel op de muren',
                    ];
                    ?>

                    @component('cooperation.tool.components.step-question', ['id' => 'how', 'translation' => 'ventilation.living_situation', 'required' => true])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'checkbox', 'inputValues' => $livingSituationValues, 'userInputValues' => null ,'userInputColumn' => 'living_situation'])
                            @foreach($livingSituationValues as $lsKey => $lsValue)

                                    <label class="checkbox">
                                        <input type="checkbox" name="building_ventilations[living_situation][]" value="{{ $lsKey }}"
                                               @if(empty(old()) &&
                                                in_array($lsKey, old('building_ventilations.living_situation',[ \App\Helpers\Hoomdossier::getMostCredibleValue($building->ventilations()->where('roof_type_id', $roofType->id), 'roof_type_id', null, \App\Helpers\Hoomdossier::getMostCredibleInputSource($building->roofTypes())) ])))
                                               checked="checked"
                                                @endif
                                        >
                                        {{ $roofType->name }}
                                    </label>

                            @endforeach
                        @endcomponent
                    @endcomponent

                </div>
            </div>
        @endif


    </form>

@endsection