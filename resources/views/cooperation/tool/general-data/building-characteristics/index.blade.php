@extends('cooperation.tool.layout')

@section('step_content')

    <form class="form-horizontal" method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.building-characteristics.store') }}" autocomplete="off">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">

                @component('cooperation.tool.components.step-question', ['id' => 'building_type_id', 'translation' => 'general-data/building-characteristics.building-type'])
                    @component('cooperation.tool.components.input-group', [
                        'inputType' => 'select',
                        'inputValues' => $buildingTypes,
                        'userInputValues' => $myBuildingFeatures,
                        'userInputModel' => 'buildingType',
                        'userInputColumn' => 'building_type_id'
                    ])
                        <select id="building_type_id" class="form-control" name="building_features[building_type_id]">
                            @foreach($buildingTypes as $buildingType)
                                <option @if($buildingType->id == old('building_features.building_type_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_type_id')))
                                        selected="selected"
                                        @endif value="{{ $buildingType->id }}">{{ $buildingType->name }}
                                </option>
                            @endforeach
                        </select>
                    @endcomponent

                @endcomponent
            </div>

            <div class="col-md-6">

                @component('cooperation.tool.components.step-question', ['id' => 'build_year', 'translation' => 'general-data/building-characteristics.build-year', 'required' => true])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'input', 'userInputValues' => $myBuildingFeatures, 'userInputColumn' => 'build_year'])
                        <input id="build_year" type="text" class="form-control" name="building_features[build_year]"
                               value="{{ old('building_features.build_year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year')) }}"
                               required autofocus>
                    @endcomponent

                @endcomponent
            </div>
        </div>

        <div class="row">

            <div id="building-characteristics" class="col-md-12">
                @include('cooperation.tool.includes.interested', ['translationKey' => 'general-data/building-characteristics.building-type.title'])

                {{--@if(count($exampleBuildings) > 0)--}}
                    <div class="row">
                        <div id="example-building" class="col-sm-12">

                            @component('cooperation.tool.components.step-question', ['id' => 'example_building_id', 'translation' => 'general-data/building-characteristics.example-building',])

                                <select id="example_building_id" class="form-control" name="buildings[example_building_id]" data-ays-ignore="true"> {{-- data-ays-ignore="true" makes sure this field is not picked up by Are You Sure --}}
                                    @foreach($exampleBuildings as $exampleBuilding)
                                        <option @if(is_null(old('buildings.example_building_id')) && is_null($building->example_building_id) && !$building->hasCompleted($step) && $exampleBuilding->is_default)
                                                selected="selected"
                                                @elseif($exampleBuilding->id == old('example_building_id'))
                                                selected="selected"
                                                @elseif ($building->example_building_id == $exampleBuilding->id)
                                                selected="selected"
                                                @endif
                                                value="{{ $exampleBuilding->id }}">{{ $exampleBuilding->name }}</option>
                                    @endforeach
                                    <option value=""
                                            <?php
                                            // if the example building is not in the $exampleBuildings collection,
                                            // we select this empty value as default.
                                            $currentNotInExampleBuildings = !$exampleBuildings->contains('id', '=', $building->example_building_id);
                                            ?>
                                            @if(empty(old('buildings.example_building_id', $building->example_building_id)) || $currentNotInExampleBuildings) selected="selected"@endif >{{ \App\Helpers\Translation::translate('general-data/building-characteristics.example-building.no-match.title') }}</option>
                                </select>

                            @endcomponent
                        </div>
                    </div>
                {{--@endif--}}

                <div class="row">
                    <div class="col-md-6">

                        @component('cooperation.tool.components.step-question', ['id' => 'surface', 'translation' => 'general-data/building-characteristics.surface', 'required' => true])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $myBuildingFeatures, 'userInputColumn' => 'surface', 'needsFormat' => true])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                                <input id="surface" type="text" class="form-control" name="building_features[surface]"
                                       value="{{ old('building_features.surface', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'surface'), 1)) }}"
                                       required autofocus>
                            @endcomponent

                        @endcomponent

                    </div>
                    <div class="col-md-6">

                        @component('cooperation.tool.components.step-question', ['id' => 'building_layers', 'translation' => 'general-data/building-characteristics.building-layers', 'required' => true])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $myBuildingFeatures, 'userInputColumn' => 'building_layers', 'needsFormat' => true, 'decimals' => 0])
                                <input id="building_layers" type="text" class="form-control" name="building_features[building_layers]"
                                       value="{{ old('building_features.building_layers', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_layers')) }}"
                                       autofocus>
                            @endcomponent

                        @endcomponent
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                        @component('cooperation.tool.components.step-question', ['id' => 'roof_type_id', 'translation' => 'general-data/building-characteristics.roof-type',])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $roofTypes, 'userInputValues' => $myBuildingFeatures, 'userInputModel' => 'roofType', 'userInputColumn' => 'roof_type_id'])
                                <select id="roof_type_id" class="form-control" name="building_features[roof_type_id]">
                                    @foreach($roofTypes as $roofType)
                                        <option
                                                @if(old('building_features.roof_type_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'roof_type_id')) == $roofType->id)
                                                selected="selected"
                                                @endif
                                                value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                    @endforeach
                                </select>
                            @endcomponent

                        @endcomponent
                    </div>
                    <div class="col-md-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'energy_label_id', 'translation' => 'general-data/building-characteristics.energy-label', 'required' => false])
                            <?php

                            // order:
                            //  1) old value
                            //  2) db value
                            //  3) default (G)

                            $selected = old('building_features.energy_label_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'energy_label_id'));

                            /*
                                if (is_null($selected)){
                                    if (isset($building->buildingFeatures->energyLabel) && $building->buildingFeatures->energyLabel instanceof \App\Models\EnergyLabel){
                                        $selected = $building->buildingFeatures->energyLabel->id;
                                    }
                                }
                                */

                            if (is_null($selected)) {
                                $selectedLabelName = 'G';
                            }
                            ?>
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $energyLabels, 'userInputValues' => $myBuildingFeatures, 'userInputModel' => 'energyLabel', 'userInputColumn' => 'energy_label_id'])
                                <select id="energy_label_id" class="form-control" name="building_features[energy_label_id]">
                                    @foreach($energyLabels as $energyLabel)
                                        <option
                                                @if(!is_null($selected) && $energyLabel->id == $selected)
                                                selected="selected"
                                                @elseif(isset($selectedLabelName) && $energyLabel->name == $selectedLabelName)
                                                selected="selected"
                                                @endif
                                                value="{{ $energyLabel->id }}">{{ $energyLabel->name }}</option>
                                    @endforeach
                                </select>
                            @endcomponent

                        @endcomponent
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group input-source-group">
                            @component('cooperation.tool.components.step-question', ['id' => 'monument', 'translation' => 'general-data/building-characteristics.monument', 'required' => false])
                                <?php
                                $checked = old('building_features.monument', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'monument'));
                                ?>
                                <label class="radio-inline">
                                    <input type="radio" name="building_features[monument]" value="1" @if($checked === 1) checked @endif>{{\App\Helpers\Translation::translate('general.options.yes.title')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="building_features[monument]" value="2" @if($checked === 2) checked @endif>{{\App\Helpers\Translation::translate('general.options.no.title')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="building_features[monument]" value="0" @if($checked === 0) checked @endif>{{\App\Helpers\Translation::translate('general.options.unknown.title')}}
                                </label>

                            @endcomponent

                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <?php
                                    // we need to check if there is a answer from one input source
                                    //$hasAnswerMonument = $myBuildingFeatures->contains('monument', '!=', '');
                                    $monumentValues = $building->buildingFeatures()->forMe()->whereNotNull('monument')->get();
                                    ?>
                                    @if($monumentValues->count() <= 0)
                                        @include('cooperation.tool.includes.no-answer-available')
                                    @else
                                        @foreach($monumentValues as $userInputValue)
                                            <?php
                                            // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
                                            $value = $userInputValue->monument;
                                            if (1 === $value) {
                                                $trans = __('woningdossier.cooperation.radiobutton.yes');
                                            } elseif (2 === $value) {
                                                $trans = __('woningdossier.cooperation.radiobutton.no');
                                            } elseif (0 === $value) {
                                                $trans = __('woningdossier.cooperation.radiobutton.unknown');
                                            }
                                            ?>

                                            <li class="change-input-value"
                                                data-input-source-short="{{$userInputValue->inputSource()->first()->short}}"
                                                data-input-value="{{ $value }}">
                                                <a href="#">{{ $userInputValue->getInputSourceName() }}: {{ $trans }}</a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
            'columnName' => 'step_comments[comment]',
            'translation' => 'general-data/building-characteristics.comment'
        ])
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            var getQualifiedExampleBuildingsRoute = '{{route('cooperation.tool.general-data.building-characteristics.qualified-example-buildings')}}';
            var exampleBuilding = $('#example_building_id');
            var defaultOptionForExampleBuilding = exampleBuilding.find('option').first();

            $(window).keydown(function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

          $('select#building_type_id').change(function () {
                handleExampleBuildingSelect($(this).val(), $('input#build_year').val())
            });

            // function to add the example buildings to the select.
            function handleExampleBuildingSelect(buildingType, buildYear) {
                $.ajax({
                    url: getQualifiedExampleBuildingsRoute,
                    data: {
                        building_type: buildingType,
                        build_year: buildYear
                    },
                    success: function (response) {
                        exampleBuilding.find('option').remove();
                        // when there are example buildings append them to the select box
                        $(response).each(function (index, exampleBuildingData) {
                            exampleBuilding.append('<option value="'+exampleBuildingData.id+'">'+exampleBuildingData.translation+'</option>')
                        });
                        // and when there is no example building add the empty one
                        if (response.length === 0) {
                            exampleBuilding.append(defaultOptionForExampleBuilding);
                        }
                    }
                })
            }

            var previous_bt = "{{ $prevBt }}";
            var previous_by = "{{ $prevBy }}";

            $("form.form-horizontal").on('submit', function () {
                // Store the current value on focus and on change
                var bt_now = $("select#building_type_id").val();
                var by_now = $("input#build_year").val();

                if (bt_now !== previous_bt || by_now !== previous_by){
                    if (previous_bt === "" && previous_by === "" || confirm('{{ \App\Helpers\Translation::translate('building-detail.warning.title') }}')) {
                        @if(App::environment('local'))
                        console.log("Building Type was changed, but it was empty or a wanted change. Proceed.");
                        @endif
                    }
                    else {
                        // the dirty class by areYouSure is removed on submit.
                        // set it back to prevent confusion for the user
                        $(this).addClass('dirty');
                        // don't submit
                        return false;
                    }
                }
            });
        });
    </script>
@endpush