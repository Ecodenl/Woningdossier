@extends('cooperation.tool.layout')

@section('step_content')

    <form  method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.building-characteristics.store') }}" autocomplete="off">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">

                @component('cooperation.tool.components.step-question', ['id' => 'building_type_id', 'translation' => 'cooperation/tool/general-data/building-characteristics.index.building-type'])
                    @component('cooperation.tool.components.input-group', [
                        'inputType' => 'select',
                        'inputValues' => $buildingTypes,
                        'userInputValues' => $myBuildingFeatures,
                        'userInputModel' => 'buildingType',
                        'userInputColumn' => 'building_type_id'
                    ])
                        <select id="building_type_id" class="form-control" name="building_features[building_type_id]" data-ays-ignore="true">
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

                @component('cooperation.tool.components.step-question', ['id' => 'build_year', 'name' => 'building_features.build_year', 'translation' => 'cooperation/tool/general-data/building-characteristics.index.build-year', 'required' => true])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'input', 'userInputValues' => $myBuildingFeatures, 'userInputColumn' => 'build_year'])
                        <input id="build_year" type="text" class="form-control" name="building_features[build_year]"
                               value="{{ old('building_features.build_year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year')) }}"
                               required autofocus
                               data-ays-ignore="true">
                    @endcomponent

                @endcomponent
            </div>
        </div>

        <div class="row">

            <div id="building-characteristics" class="col-md-12">

                    <div class="row">
                        <div id="example-building" class="col-sm-12 @if($exampleBuildings->count() == 0) d-none @endif ">

                            @component('cooperation.tool.components.step-question', ['id' => 'example_building_id', 'translation' => 'cooperation/tool/general-data/building-characteristics.index.example-building',])

                                <select id="example_building_id" data-ays-ignore="true" class="form-control" name="buildings[example_building_id]"> {{-- data-ays-ignore="true" makes sure this field is not picked up by Are You Sure --}}
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
                                            @if(empty(old('buildings.example_building_id', $building->example_building_id)) || $currentNotInExampleBuildings) selected="selected"@endif >@lang('cooperation/tool/general-data/building-characteristics.index.example-building.no-match.title') </option>
                                </select>

                            @endcomponent
                        </div>
                    </div>

                <div class="row">
                    <div class="col-md-6">

                        @component('cooperation.tool.components.step-question', ['id' => 'surface', 'name' => 'building_features.surface', 'translation' => 'cooperation/tool/general-data/building-characteristics.index.surface', 'required' => true])

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

                        @component('cooperation.tool.components.step-question', ['id' => 'building_features.building_layers', 'translation' => 'cooperation/tool/general-data/building-characteristics.index.building-layers', 'required' => true])

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

                        @component('cooperation.tool.components.step-question', ['id' => 'building_features.roof_type_id', 'translation' => 'cooperation/tool/general-data/building-characteristics.index.roof-type',])

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
                        @component('cooperation.tool.components.step-question', ['id' => 'building_features.energy_label_id', 'translation' => 'cooperation/tool/general-data/building-characteristics.index.energy-label', 'required' => false])
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
                            @component('cooperation.tool.components.step-question', ['id' => 'building_features.monument', 'translation' => 'cooperation/tool/general-data/building-characteristics.index.monument', 'required' => false])
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
            'translation' => 'cooperation/tool/general-data/building-characteristics.index.comment'
        ])
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            var getQualifiedExampleBuildingsRoute = '{{route('cooperation.tool.general-data.building-characteristics.qualified-example-buildings')}}';
            var storeBuildingTypeId = '{{ route('cooperation.tool.building-type.store') }}';
            var storeExampleBuildingRoute = '{{route('cooperation.tool.example-building.store')}}';
            var exampleBuilding = $('#example_building_id');
            var buildingType = $('#building_type_id');
            var buildYear = $('#build_year');
            var defaultOptionForExampleBuilding = exampleBuilding.find('option').last();

            var previousBuildingType = buildingType.val();
            var previousBuildYear = buildYear.val();
            var previousExampleBuilding = exampleBuilding.val();
            previousExampleBuilding = isNaN(previousExampleBuilding) ? "" : previousExampleBuilding;


            $('#build_year, #building_type_id').change(function () {
                if (confirm('{{__('cooperation/tool/general-data/building-characteristics.index.building-type.are-you-sure.title')}}')) {
                    $.ajax({
                        type: "POST",
                        url: storeBuildingTypeId,
                        data: {
                            building_type_id: buildingType.val(),
                            build_year: buildYear.val(),
                        },
                        success: function (data) {
                            handleExampleBuildingSelect(buildingType.val(), buildYear.val());
                            storeExampleBuilding(buildingType.val(), buildYear.val());

                            // update the previous values
                            previousBuildingType = buildingType.val();
                            previousBuildYear = buildYear.val();
                        }
                    });
                } else {
                    // user canceled the operation so we set back the option
                    buildingType.val(previousBuildingType);
                    buildYear.val(previousBuildYear);
                    reInitCurrentForm();

                }
            });
            
            function storeExampleBuilding(buildingTypeId, buildYear, exampleBuildingId = null)
            {
                @if(App::environment('local'))
                    console.log('Example building is getting stored');
                @endif
                exampleBuildingId = isNaN(exampleBuildingId) ? "" : exampleBuildingId;
                console.log(`The example building id thats saved: ${exampleBuildingId}`);
                $.ajax({
                    type: "POST",
                    url: storeExampleBuildingRoute,
                    data: {
                        example_building_id: exampleBuildingId,
                        building_type_id: buildingTypeId,
                        build_year: buildYear,
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }

            // function to add the example buildings to the select.
            function handleExampleBuildingSelect(buildingTypeId, buildYear) {
                $.ajax({
                    url: getQualifiedExampleBuildingsRoute,
                    data: {
                        building_type_id: buildingTypeId,
                        build_year: buildYear
                    },
                    success: function (response) {
                        exampleBuilding.find('option').remove();
                        // and when there is no example building add the empty one
                        if (response.length === 0) {
                            $(exampleBuilding).parents().find('#example-building').hide();
                            exampleBuilding.append(defaultOptionForExampleBuilding);
                        } else {
                            exampleBuilding.append(defaultOptionForExampleBuilding);

                            $(exampleBuilding).parents().find('#example-building').show();
                            // when there are example buildings append them to the select box
                            $(response).each(function (index, exampleBuildingData) {
                                exampleBuilding.append('<option value="'+exampleBuildingData.id+'">'+exampleBuildingData.translation+'</option>')
                            });
                        }
                    }
                })
            }


            exampleBuilding.change(function () {
                var currentExampleBuildingId = parseInt(this.value);

                console.log(`on change eb id: ${currentExampleBuildingId}`);
                // do something with the previous value after the change
                // when the user changed the eb, apply it after the confirm
                if (currentExampleBuildingId !== previousExampleBuilding) {

                    if (confirm('{{ __('cooperation/tool/general-data/building-characteristics.index.example-building.apply-are-you-sure.title') }}')) {
                        @if(App::environment('local'))
                        // console.log("Let's save it. EB id: " + currentExampleBuildingId);
                        @endif

                        reInitCurrentForm();

                        storeExampleBuilding(buildingType.val(), buildYear.val(), currentExampleBuildingId);

                        // Make sure the previous value is updated
                        previousExampleBuilding = currentExampleBuildingId;
                    } else {
                        exampleBuilding.val(previousExampleBuilding);
                    }
                }
            });

            // comes in handy when a user action is canceled.
            // technically the form changed, but for the used nothing changed so reinit
            function reInitCurrentForm()
            {
                $(this).closest('form').trigger('reinitialize.areYouSure');
            }


        });
    </script>
@endpush