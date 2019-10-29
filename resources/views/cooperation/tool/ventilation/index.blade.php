@extends('cooperation.tool.layout')

{{--@section('step_title', \App\Helpers\Translation::translate('ventilation-information.title.title'))--}}
@section('step_title', 'Natuurlijke ventilatie')

@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.ventilation-information.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}

        <div class="row">
            <div class="col-sm-12">
                <p style="margin-left: -5px">
                    U heeft aangegeven dat er in de woning natuurlijke ventilatie aanwezig is. In een huis met natuurlijke ventilatie zit geen mechanisch ventilatiesysteem, dat betekent dat er alleen via natuurlijke weg geventileerd kan worden door ventilatieroosters en bijvoorbeeld ramen of deuren.
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
                        'other' => 'Ventilatieroosters overig',
                        'windows' => '(Klep)ramen',
                        'none' => 'Geen ventilatievoorzieningen',
                    ];
                ?>

                @component('cooperation.tool.components.step-question', ['id' => 'how', 'translation' => 'ventilation.how', 'required' => true])
                    @component('cooperation.tool.components.input-group',
                ['inputType' => 'select', 'inputValues' => $howValues, 'userInputValues' => null ,'userInputColumn' => 'how'])
                        <select id="how" class="form-control" name="how">
                            @foreach($howValues as $howKey => $howValue)
                                <option @if(old('how', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'how'))  == $howKey) selected="selected" @endif value="{{ $howKey }}">{{ $howValue }}</option>
                            @endforeach
                        </select>@endcomponent
                @endcomponent

            </div>
        </div>
        @endif

        <!-- living_situation: natural, mechanic,


    </form>

@endsection