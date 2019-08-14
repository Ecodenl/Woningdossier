@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('general-data.title.title'))

@section('step_content')

    <form class="form-horizontal" method="POST" id="main-form"
          action="{{ route('cooperation.tool.general-data.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div class="row">

            <div id="building-type" class="col-md-12">
                @include('cooperation.tool.includes.interested', ['translationKey' => 'general-data.building-type.title'])

                @if(count($exampleBuildings) > 0)
                    <div class="row">
                        <div id="example-building" class="col-sm-12">

                            @component('cooperation.tool.components.step-question', ['id' => 'example_building_id', 'translation' => 'general-data.example-building',])

                                <select id="example_building_id" class="form-control" name="example_building_id"
                                        data-ays-ignore="true"> {{-- data-ays-ignore="true" makes sure this field is not picked up by Are You Sure --}}
                                    @foreach($exampleBuildings as $exampleBuilding)
                                        <option @if(is_null(old('example_building_id')) && is_null($building->example_building_id) && !$building->hasCompleted($step) && $exampleBuilding->is_default)
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
                                            @if(empty(old('example_building_id', $building->example_building_id)) || $currentNotInExampleBuildings) selected="selected"@endif >{{ \App\Helpers\Translation::translate('general-data.example-building.no-match.title') }}</option>
                                </select>

                            @endcomponent
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">

                        @component('cooperation.tool.components.step-question', ['id' => 'surface', 'translation' => 'general-data.building-type.what-user-surface', 'required' => true])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'surface', 'needsFormat' => true])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                                <input id="surface" type="text" class="form-control" name="surface"
                                       value="{{ old('surface', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'surface'), 1)) }}"
                                       required autofocus>
                            @endcomponent

                        @endcomponent

                    </div>
                    <div class="col-md-6">

                        @component('cooperation.tool.components.step-question', ['id' => 'building_layers', 'translation' => 'general-data.building-type.how-much-building-layers', 'required' => true])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'building_layers', 'needsFormat' => true, 'decimals' => 0])
                                <input id="building_layers" type="text" class="form-control" name="building_layers"
                                       value="{{ old('building_layers', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_layers')) }}"
                                       autofocus>
                            @endcomponent

                        @endcomponent
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                        @component('cooperation.tool.components.step-question', ['id' => 'roof_type_id', 'translation' => 'general-data.building-type.type-roof',])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $roofTypes, 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputModel' => 'roofType', 'userInputColumn' => 'roof_type_id'])
                                <select id="roof_type_id" class="form-control" name="roof_type_id">
                                    @foreach($roofTypes as $roofType)
                                        <option
                                                @if(old('roof_type_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'roof_type_id')) == $roofType->id)
                                                selected="selected"
                                                @endif
                                                value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                    @endforeach
                                </select>
                            @endcomponent

                        @endcomponent
                    </div>
                    <div class="col-md-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'energy_label_id', 'translation' => 'general-data.building-type.current-energy-label', 'required' => false])
                            <?php

                            // order:
                            //  1) old value
                            //  2) db value
                            //  3) default (G)

                            $selected = old('energy_label_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'energy_label_id'));

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
                            ['inputType' => 'select', 'inputValues' => $energyLabels, 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputModel' => 'energyLabel', 'userInputColumn' => 'energy_label_id'])
                                <select id="energy_label_id" class="form-control" name="energy_label_id">
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
                            @component('cooperation.tool.components.step-question', ['id' => 'monument', 'translation' => 'general-data.building-type.is-monument', 'required' => false])
                                <?php $checked = (int)old('monument', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'monument')); ?>
                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="1"
                                           @if($checked == 1) checked @endif>{{\App\Helpers\Translation::translate('general.options.yes.title')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="2"
                                           @if($checked == 2) checked @endif>{{\App\Helpers\Translation::translate('general.options.no.title')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="0"
                                           @if($checked == 0) checked @endif>{{\App\Helpers\Translation::translate('general.options.unknown.title')}}
                                </label>

                            @endcomponent

                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <?php
                                    // we need to check if there is a answer from one input source
                                    $hasAnswerMonument = $building->buildingFeatures()->forMe()->get()->contains('monument', '!=', '');

                                    ?>
                                    @if(!$hasAnswerMonument)
                                            @include('cooperation.tool.includes.no-answer-available')
                                    @else
                                        @foreach($building->buildingFeatures()->forMe()->get() as $userInputValue)
                                            <?php
                                            // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
                                            $value = $userInputValue->monument;
                                            if (1 === $value) {
                                                $trans = __('woningdossier.cooperation.radiobutton.yes');
                                            } elseif (2 === $value) {
                                                $trans = __('woningdossier.cooperation.radiobutton.no');
                                            } else {
                                                $trans = __('woningdossier.cooperation.radiobutton.unknown');
                                            }
                                            ?>

                                            <li class="change-input-value"
                                                data-input-source-short="{{$userInputValue->inputSource()->first()->short}}"
                                                data-input-value="{{ $value }}"><a
                                                        href="#">{{ $userInputValue->getInputSourceName() }}
                                                    : {{ $trans }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div id="energy-saving-measures">
            @include('cooperation.tool.includes.section-title', [
                'translation' => 'general-data.energy-saving-measures.title',
                'id' => 'energy-saving-measures'
            ])

            @foreach($elements as $i => $element)
                <?php
                /** @var \App\Models\UserInterest|null $elementInterestForCurrentUser holds the interest for the current inputsource for a resident */
                $elementInterestForCurrentUser = $userInterestsForMe
                    ->where('interested_in_type', 'element')
                    ->where('input_source_id', \App\Helpers\HoomdossierSession::getInputSource())
                    ->where('interested_in_id', $element->id)
                    ->first();
                ?>
                @if ($i % 2 == 0)
                    <div class="row">
                        @endif
                        <div class="@if(in_array($element->short, ['sleeping-rooms-windows', 'living-rooms-windows'])) col-sm-6 @else col-sm-4 @endif">
                            <div class="form-group add-space{{ $errors->has('element.'.$element->id) ? ' has-error' : '' }}">
                                <label for="element_{{ $element->id }}" class="control-label">
                                    <i data-toggle="modal" data-target="#element_{{ $element->id }}-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>
                                    @lang('general-data.element.'.$element->short.'.title')
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $element->values()->orderBy('order')->get(), 'userInputValues' => $building->buildingElements()->forMe()->where('element_id', $element->id)->get(), 'userInputColumn' => 'element_value_id'])
                                    <select id="element_{{ $element->id }}" class="form-control"
                                            name="element[{{ $element->id }}]">
                                        @foreach($element->values()->orderBy('order')->get() as $elementValue)
                                            {{--<option @if($elementValue->id == old('element.'.$element->id.'')) selected="selected"
                                                    @elseif($building->buildingElements()->where('element_id', $element->id)->first() && $building->buildingElements()->where('element_id', $element->id)->first()->element_value_id == $elementValue->id) selected
                                                    @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option> --}}
                                            <option @if(old('element.' . $element->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $element->id), 'element_value_id')) == $elementValue->id) selected="selected"
                                                    @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                                        @endforeach
                                    </select>
                                @endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    @lang('general-data.element.'.$element->short.'.help')
                                @endcomponent

                                @if ($errors->has('element.' . $element->id))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('element.' . $element->id) }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if(!in_array($element->short, ['sleeping-rooms-windows', 'living-rooms-windows']))
                            <div class="col-sm-2">
                                @component('cooperation.tool.components.step-question', ['id' => 'user_interest.element.' . $element->id, 'translation' => 'general.interested-in-improvement', 'required' => true, 'labelStyling' => 'font-size:12px;', 'labelClass' => 'user-interest-label'])
                                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', 'element')->where('interested_in_id', $element->id),  'userInputColumn' => 'interest_id'])
                                        <select id="user_interest_element_{{ $element->id }}" class="form-control"
                                                name="user_interest[element][{{ $element->id }}]">
                                            @foreach($interests as $interest)
                                                <option @if($interest->id == old('user_interest.element.'. $element->id, \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->interests()->where('interested_in_type', 'element')->where('interested_in_id', $element->id), 'interest_id'))) selected="selected"
                                                        @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                            @endforeach
                                        </select>
                                    @endcomponent

                                @endcomponent
                            </div>
                        @endif
                        @if ($i % 2 == 1)
                    </div>
                @endif
            @endforeach

            {{-- note that because of ->count, odd counts % 2 result in 1, whereas $i starts with 0 and therefore has the inverted result --}}
            @if($elements->count() % 2 == 1)
                {{-- last (row) div was not closed. Close it --}}
        </div>
        @endif


        @foreach($services as $i => $service)
            <?php
            /** @var \App\Models\UserInterest|null $serviceInterestForCurrentUser holds the interest for the current input source for a resident */
            $serviceInterestForCurrentUser = $userInterestsForMe
                ->where('interested_in_type', 'service')
                ->where('input_source_id', \App\Helpers\HoomdossierSession::getInputSource())
                ->where('interested_in_id', $service->id)
                ->first();
            ?>
            @if ( ($i % 2 == 0 && $service->short != "boiler") || $service->short == 'total-sun-panels')
                <div class="row" id="service_row_{{$service->id}}">
                    @elseif(strpos($service->name, 'geventileerd'))
                </div>
                <div class="row">
                    @endif
                    @if($service->short == "hr-boiler")
                        <div class="row">
                            @endif
                            <div class="col-sm-4">
                                <div class="form-group add-space{{ $errors->has('service.'.$service->id) ? ' has-error' : '' }}">
                                    <label for="{{ $service->short }}" class="control-label">
                                        <i data-toggle="modal" data-target="#service_{{ $service->id }}-info"
                                           class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                           aria-expanded="false"></i>
                                        @lang('general-data.service.'.$service->short.'.title')
                                    </label>
                                    {{-- This will check if the service has values. If so we need a selectbox. If not: a textbox --}}
                                    @if($service->values()->where('service_id', $service->id)->first() != null)

                                        <?php
                                        $selectedSV = old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $service->id), 'service_value_id'));

                                        if (is_null($selectedSV)) {
                                            /** @var \App\Models\Service $service */
                                            $sv = $service->values()->where('is_default', true)->first();
                                            if ($sv instanceof \App\Models\ServiceValue) {
                                                $selectedSV = $sv->id;
                                            }
                                        }
                                        ?>


                                        @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'select', 'inputValues' => $service->values()->orderBy('order')->get(), 'userInputValues' => $building->buildingServices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])
                                            <select id="{{ $service->short }}" class="form-control"
                                                    name="service[{{ $service->id }}]">
                                                @foreach($service->values()->orderBy('order')->get() as $serviceValue)
                                                    <option @if($serviceValue->id == $selectedSV) selected="selected"
                                                            @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                                                    {{--<option @if(old('service.'.$service->id) == $serviceValue->id) selected="selected" @elseif($building->buildingServices()->where('service_id', $service->id)->first() != null && $building->buildingServices()->where('service_id', $service->id)->first()->service_value_id == $serviceValue->id) selected @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>--}}
                                                @endforeach
                                            </select>
                                        @endcomponent
                                    @else
                                        @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'input', 'userInputValues' => $building->buildingServices()->forMe()->where('service_id', $service->id)->get(),'userInputColumn' => 'extra.value'])
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.pieces')</span>
                                            <input type="text" id="{{ $service->short }}" class="form-control"
                                                   value="{{ old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $service->id), 'extra.value')) }}"
                                                   name="service[{{ $service->id }}]">
                                            {{--<input type="text" id="{{ $service->short }}" class="form-control" value="@if(old('service.' . $service->id )){{old('service.' . $service->id)}} @elseif(isset($building->buildingServices()->where('service_id', $service->id)->first()->extra['value'])){{$building->buildingServices()->where('service_id', $service->id)->first()->extra['value']}} @endif" name="service[{{ $service->id }}]">--}}
                                        @endcomponent
                                    @endif

                                    @component('cooperation.tool.components.help-modal')
                                        @lang('general-data.service.'.$service->short.'.help')
                                    @endcomponent

                                    @if ($errors->has('service.' . $service->id))
                                        <span class="help-block">
                                <strong>{{ $errors->first('service.' . $service->id) }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>
                            {{-- interest is not asked for current boiler --}}
                            @if($service->short != 'boiler')
                                <div class="col-sm-2">
                                    @component('cooperation.tool.components.step-question', ['id' => 'user_interest.service.' . $service->id, 'translation' => 'general.interested-in-improvement', 'required' => true, 'labelStyling' => 'font-size:12px;', 'labelClass' => 'user-interest-label'])
                                        @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', 'service')->where('interested_in_id', $service->id),  'userInputColumn' => 'interest_id'])
                                            <select id="user_interest_service_{{ $service->id }}"
                                                    class="form-control"
                                                    name="user_interest[service][{{ $service->id }}]">
                                                @foreach($interests as $interest)
                                                    <option @if($interest->id == old('user_interest.service.'. $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->interests()->where('interested_in_type', 'service')->where('interested_in_id', $service->id), 'interest_id'))) selected="selected"
                                                            @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                                @endforeach
                                            </select>
                                        @endcomponent
                                    @endcomponent

                                </div>
                            @endif

                            @if(strpos($service->name, 'geventileerd') || $service->short == "total-sun-panels")
                                <div class="col-sm-6 {{ $errors->has(''.$service->id.'.extra') ? ' show' : '' }}">

                                    <div id="{{$service->id.'-extra'}}">
                                    @if(strpos($service->name, 'geventileerd'))
                                        <?php $translationKey = 'general-data.energy-saving-measures.house-ventilation.if-mechanic' ?>
                                    @elseif($service->short == 'total-sun-panels')
                                        <?php $translationKey = 'general-data.energy-saving-measures.solar-panels.if-yes' ?>
                                    @endif
                                    @component('cooperation.tool.components.step-question', ['id' => $service->id.'.extra', 'translation' => $translationKey, 'required' => false])

                                        <?php
                                        if (isset($building->buildingServices()->where('service_id', $service->id)->first()->extra['year'])) {
                                            $year = $building->buildingServices()->where('service_id', $service->id)->first()->extra['year'];
                                        }
                                        ?>

                                        @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'input', 'userInputValues' => $building->buildingServices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'extra.year'])
                                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                                            <input type="text" class="form-control"
                                                   name="{{ $service->id }}[extra][year]"
                                                   value="{{ old($service->id . '.extra.year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $service->id), 'extra.year')) }}">
                                            {{--<input type="text" class="form-control" name="{{ $service->id }}[extra][year]" value="@if(old($service->id.'.extra.year')){{ old($service->id.'.extra.year') }}@elseif(isset($year)){{ $year }}@endif">--}}
                                        @endcomponent

                                        {{--@component('cooperation.tool.components.help-modal')--}}
                                        {{--                                                    {{\App\Helpers\Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.help')}}--}}
                                    @endcomponent
                                    </div>

                                </div>
                            @endif


                            @if (( $i % 2 == 1 && $service->short != "hr-boiler")  || strpos($service->name, 'geventileerd') || $service->short == "sun-boiler")
                        </div>
                    @endif
                    @endforeach

                    {{-- note that because of ->count, odd counts % 2 result in 1, whereas $i starts with 0 and therefore has the inverted result --}}
                    @if($services->count() % 2 == 1)
                        {{-- last (row) div was not closed. Close it --}}
                </div>
                @endif
                {{-- Close the measure div --}}
                </div>



                <div id="data-about-usage">
                    @include('cooperation.tool.includes.section-title', [
                        'translation' => 'general-data.data-about-usage.title',
                        'id' => 'data-about-usage'
                    ])
                    <div class="row">
                        <div class="col-sm-6">

                            @component('cooperation.tool.components.step-question', ['id' => 'resident_count', 'translation' => 'general-data.data-about-usage.total-citizens', 'required' => true])

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'resident_count'])
                                    <input type="text" id="resident_count" class="form-control"
                                           value="{{ old('resident_count', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'resident_count')) }}"
                                           name="resident_count" required>
                                    {{--<input type="text" id="resident_count" class="form-control" value="@if(old('resident_count') != ""){{old('resident_count')}}@elseif(isset($energyHabit)){{$energyHabit->resident_count}}@endif" name="resident_count" required>--}}
                                @endcomponent
                            @endcomponent

                        </div>

                        <div class="col-sm-6">

                            @component('cooperation.tool.components.step-question', ['id' => 'cook_gas', 'translation' => 'general-data.data-about-usage.cooked-on-gas', 'required' => true])

                                <div class="input-group input-source-group">
                                    <label class="radio-inline">
                                        <input type="radio" name="cook_gas" required="required"
                                               @if(old('cook_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'cook_gas')) == 1) checked
                                               @endif value="1">{{\App\Helpers\Translation::translate('general.options.yes.title')}}
                                        {{--<input type="radio" name="cook_gas" @if(old('cook_gas') == 1) checked @elseif(isset($energyHabit) && $energyHabit->cook_gas == 1) checked @endif  value="1">@lang('woningdossier.cooperation.radiobutton.yes')--}}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="cook_gas" required="required"
                                               @if(old('cook_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'cook_gas')) == 2) checked
                                               @endif value="2">{{\App\Helpers\Translation::translate('general.options.no.title')}}
                                    </label>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle"
                                                data-toggle="dropdown"><span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <?php
                                            // we need to check if there is a answer from one input source
                                            $hasAnswerCookGas = $userEnergyHabitsForMe->contains('cook_gas', '!=', '');

                                            ?>
                                            @if(!$hasAnswerCookGas)
                                                    @include('cooperation.tool.includes.no-answer-available')
                                            @else
                                            @foreach($userEnergyHabitsForMe as $userInputValue)
                                                <?php
                                                // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
                                                $value = $userInputValue->cook_gas;
                                                if (1 === $value) {
                                                    $trans = __('woningdossier.cooperation.radiobutton.yes');
                                                } elseif (2 === $value) {
                                                    $trans = __('woningdossier.cooperation.radiobutton.no');
                                                }
                                                ?>
                                                <li class="change-input-value"
                                                    data-input-source-short="{{$userInputValue->inputSource()->first()->short}}"
                                                    data-input-value="{{ $value }}"><a
                                                            href="#">{{ $userInputValue->getInputSourceName() }}
                                                        : {{ $trans }}</a></li>
                                            @endforeach
                                                @endif
                                        </ul>
                                    </div>
                                </div>
                            @endcomponent

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'thermostat_high', 'translation' => 'general-data.data-about-usage.thermostat-highest', 'required' => false])
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'thermostat_high', 'needsFormat' => true])
                                    <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.degrees.title')}}</span>
                                    <input type="text" id="thermostat_high" class="form-control"
                                           value="{{ old('thermostat_high', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'thermostat_high', 20), 1)) }}"
                                           name="thermostat_high">
                                @endcomponent

                            @endcomponent

                        </div>


                        <div class="col-sm-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'thermostat_low', 'translation' => 'general-data.data-about-usage.thermostat-lowest', 'required' => false])
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'thermostat_low', 'needsFormat' => true])
                                    <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.degrees.title')}}</span>
                                    <input id="thermostat_low" type="text" class="form-control" name="thermostat_low"
                                           value="{{ old('thermostat_low', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'thermostat_low', 16), 1)) }}">
                                @endcomponent
                            @endcomponent

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'hours_high', 'translation' => 'general-data.data-about-usage.max-hours-thermostat-highest', 'required' => false])

                                <?php
                                $hours = range(1, 24);
                                $selectedHours = old('hours_high', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'hours_high', 12));
                                // We have to prepend the value so the key => value pairs are in order for the input group addon
                                $inputValues = $hours;
                                array_unshift($inputValues, __('woningdossier.cooperation.radiobutton.not-important'));
                                ?>
                                @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $inputValues, 'userInputValues' => $userEnergyHabitsForMe, 'userInputModel' => 'UserEnergyHabit', 'userInputColumn' => 'hours_high'])
                                    <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.hours')</span>
                                    <select id="hours_high" class="form-control" name="hours_high">
                                        @foreach($hours as $hour)
                                            <option @if($hour === $selectedHours) selected
                                                    @endif value="{{ $hour }}">{{ $hour }}</option>
                                            {{--<option @if($hour === old('hours_high')) selected @elseif(isset($energyHabit) && $energyHabit->hours_high == $hour) selected @elseif(!isset($energyHabit) && $hour == 12) selected @endif value="{{ $hour }}">{{ $hour }}</option>--}}
                                        @endforeach
                                        <option @if(0 === $selectedHours) selected @endif value="0">
                                            {{--<option @if($hour === old('hours_high')) selected @elseif(isset($energyHabit) && $energyHabit->hours_high == 0) selected @endif value="0">--}}
                                            @lang('woningdossier.cooperation.radiobutton.not-important')
                                        </option>
                                    </select>
                                @endcomponent
                            @endcomponent
                        </div>

                        <div class="col-sm-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'heating_first_floor', 'translation' => 'general-data.data-about-usage.situation-first-floor', 'required' => false])
                                <?php

                                $bhDefault = $buildingHeatings->where('is_default', '=', true)->first();
                                if ($bhDefault instanceof \App\Models\BuildingHeating) {
                                    $defaultHFF = $bhDefault->id;
                                }

                                $selectedHFF = old('heating_first_floor', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'heating_first_floor', $defaultHFF));


                                ?>

                                @component('cooperation.tool.components.input-group',['inputType' => 'select', 'inputValues' => $buildingHeatings, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'heating_first_floor'])
                                    <select id="heating_first_floor" class="form-control" name="heating_first_floor">
                                        @foreach($buildingHeatings as $buildingHeating)
                                            <option @if($buildingHeating->id == $selectedHFF) selected="selected"
                                                    @endif value="{{ $buildingHeating->id}}">{{ $buildingHeating->name }}</option>
                                        @endforeach
                                    </select>
                                @endcomponent

                            @endcomponent
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'heating_second_floor', 'translation' => 'general-data.data-about-usage.situation-second-floor', 'required' => false])
                                <?php

                                $bhDefault = $buildingHeatings->where('is_default', '=', true)->first();
                                if ($bhDefault instanceof \App\Models\BuildingHeating) {
                                    $defaultHSF = $bhDefault->id;
                                }

                                $selectedHSF = old('heating_second_floor', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'heating_second_floor', $defaultHSF));

                                ?>

                                @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $buildingHeatings, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'heating_second_floor'])
                                    <select id="heating_second_floor" class="form-control"
                                            name="heating_second_floor">
                                        @foreach($buildingHeatings as $buildingHeating)
                                            <option @if($buildingHeating->id == $selectedHSF) selected="selected"
                                                    @endif value="{{ $buildingHeating->id }}">{{ $buildingHeating->name }}</option>
                                        @endforeach
                                    </select>
                                @endcomponent
                            @endcomponent

                        </div>

                        <div class="col-sm-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'water_comfort', 'translation' => 'general-data.data-about-usage.comfortniveau-warm-tapwater', 'required' => false])
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $comfortLevelsTapWater, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'water_comfort_id'])
                                    <select id="water_comfort" class="form-control" name="water_comfort">
                                        @foreach($comfortLevelsTapWater as $comfortLevelTapWater)
                                            <option @if(old('water_comfort', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'water_comfort_id')) == $comfortLevelTapWater->id) selected="selected"
                                                    @endif value="{{ $comfortLevelTapWater->id }}">{{ $comfortLevelTapWater->name }}</option>
                                            {{--<option @if($comfortLevelTapWater->id == old('water_comfort')) selected @elseif(isset($energyHabit) && $energyHabit->water_comfort_id == $comfortLevelTapWater->id) selected @endif value="{{ $comfortLevelTapWater->id }}">{{ $comfortLevelTapWater->name }}</option>--}}
                                        @endforeach
                                    </select>
                                @endcomponent
                            @endcomponent

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'amount_electricity', 'translation' => 'general-data.data-about-usage.electricity-consumption-past-year', 'required' => true])

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'amount_electricity'])
                                    <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.kwh')</span>
                                    <input id="amount_electricity" type="text"
                                           value="{{ old('amount_electricity', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'amount_electricity')) }}"
                                           class="form-control" name="amount_electricity" required>
                                @endcomponent

                            @endcomponent

                        </div>
                        <div class="col-sm-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'amount_gas', 'translation' => 'general-data.data-about-usage.gas-usage-past-year', 'required' => true])

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'amount_gas'])
                                    <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.cubic-meters.title')}}</span>
                                    <input id="amount_gas" type="text"
                                           value="{{ old('amount_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'amount_gas')) }}"
                                           class="form-control" name="amount_gas" required>
                                @endcomponent

                            @endcomponent


                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            @component('cooperation.tool.components.step-question', ['id' => 'living_situation_extra', 'translation' => 'general-data.data-about-usage.additional-info', 'required' => false])
                                <textarea id="additional-info" class="form-control"
                                          name="living_situation_extra">{{ old('living_situation_extra', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'living_situation_extra')) }}</textarea>
                            @endcomponent
                        </div>
                    </div>
                    @include('cooperation.tool.includes.comment', [
                        'collection' => $userEnergyHabitsForMe,
                        'commentColumn' => 'living_situation_extra',
                        'translation' => [
                            'title' => 'general-data.data-about-usage.additional-info.title',
                            'help' => 'general-data.data-about-usage.additional-info.help'
                        ]
                    ])

                    <div class="row">
                        <div class="col-sm-12">
                            @include('cooperation.tool.includes.section-title', [
                                'translation' => 'general-data.motivation.title',
                                'id' => 'motivation'
                            ])
                        </div>

                        {{-- Start at 1 so the translation will too. --}}
                        @for($i = 1; $i < 5; $i++)
                            <div class="col-sm-6">
                                @component('cooperation.tool.components.step-question', ['id' => 'motivation.'.$i, 'translation' => 'general-data.motivation.priority', 'translationReplace' => ['prio' => $i], 'required' => false])
                                    <select id="motivation[{{ $i }}]" class="form-control"
                                            name="motivation[{{ $i }}]">

                                        @if($energyHabit != null)
                                            @foreach($motivations as $motivation)
                                                <option
                                                        @if($motivation->id == old('motivation.'.$i))
                                                        selected
                                                        @elseif(old() == false && isset(Auth::user()->motivations()->where('order', $i)->first()->motivation_id) && Auth::user()->motivations()->where('order', $i)->first()->motivation_id == $motivation->id)
                                                        selected
                                                        @endif value="{{ $motivation->id }}">{{ $motivation->name }}
                                                </option>
                                            @endforeach

                                        @else
                                            @foreach($motivations as $motivation)
                                                <option @if($motivation->id == old('motivation.'.$i)) selected
                                                        @endif value="{{$motivation->id}}">{{$motivation->name}}</option>
                                            @endforeach
                                        @endif


                                    </select>
                                @endcomponent

                            </div>
                        @endfor
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            @component('cooperation.tool.components.step-question', ['id' => 'motivation_extra', 'translation' => 'general-data.motivation-extra', 'required' => false])
                                <textarea id="motivation-extra" class="form-control"
                                          name="motivation_extra">@if(old('motivation_extra') != ""){{ old('motivation_extra') }}@elseif(isset($energyHabit)){{ $energyHabit->motivation_extra }}@endif</textarea>

                            @endcomponent

                        </div>
                    </div>
                    @include('cooperation.tool.includes.comment', [
                            'collection' => $userEnergyHabitsForMe,
                            'commentColumn' => 'motivation_extra',
                            'translation' => [
                                'title' => 'general-data.motivation-extra.title',
                                'help' => 'general-data.motivation-extra.help'
                            ]
                    ])


                    @if(!\App\helpers\HoomdossierSession::isUserObserving())
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                            <div class="form-group add-space">
                                <div class="">
                                    <button type="submit" class="pull-right btn btn-primary">
                                        @lang('default.buttons.next')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            var previous_eb = parseInt('{{ $building->example_building_id }}');

            $("select#example_building_id").change(function () {
                var current_eb = parseInt(this.value);
                // if "no specific": set to null
                current_eb = isNaN(current_eb) ? null : current_eb;
                // Do something with the previous value after the change
                if (current_eb !== previous_eb) {
                    if (previous_eb === "" || confirm('{{ \App\Helpers\Translation::translate('general-data.example-building.apply-are-you-sure.title') }}')) {
                        @if(App::environment('local'))
                        console.log("Let's save it. EB id: " + current_eb);
                        @endif

                        // Firefox fix, who else thinks that stuff has changed
                        $(this).closest('form').trigger('reinitialize.areYouSure');
                        // End Firefox fix

                        $.ajax({
                            type: "POST",
                            url: '{{ route('cooperation.tool.apply-example-building', compact('cooperation')) }}',
                            data: {example_building_id: current_eb},
                            success: function (data) {
                                location.reload();
                            }
                        });


                        // Make sure the previous value is updated
                        previous_eb = current_eb;
                    } else {
                        if ($("#select-box option[value='" + previous_eb + "']").val() === undefined) {
                            @if(App::environment('local'))
                            console.log("Prev: " + previous_eb + " does not exist. Setting to empty");
                            @endif
                            $(this).val("");
                        } else {
                            $(this).val(previous_eb);
                        }
                    }
                }
            });

            // Check if the house ventilation is mechanic
            $(document).change('#house-ventilation', function () {

                // Housse ventilation
                var houseVentilation = $('#house-ventilation option:selected').text();

                // text wont change, id's will
                if (houseVentilation === "Mechanisch" || houseVentilation === "Decentraal mechanisch") {
                    $('#house-ventilation').parent().parent().parent().next().next().show();
                } else {
                    $('#house-ventilation').parent().parent().parent().next().next().hide();
                    if ($('#house-ventilation').parent().parent().parent().next().next().find('input').val().trim() !== "") {
                        console.log("Adjusting house ventilation");
                        $('#house-ventilation').parent().parent().parent().next().next().find('input').val("");
                    }
                }
            });

            // check if a user is interested in a sun panel
            $(document).change('#total-sun-panels', function () {
                var input = $("#total-sun-panels");
                var totalSunPanels = parseInt(input.val());
                var extraField = input.parent().parent().parent().next().next();

                if (totalSunPanels > 0) {
                    extraField.show();
                } else {
                    extraField.hide();
                    if (extraField.find('input').val().trim() !== "") {
                        console.log("Adjusting sun panel year");
                        extraField.find('input').val("");
                    }
                }

            });

            $(document).change('#hr-boiler', function () {
                if (parseInt($('#hr-boiler').val()) === 13) {
                    // hide the input for the type of boiler
                    //$('#boiler').parent().hide();
                    // Hide the interest input
                    //$('#boiler').parent().parent().next().hide();
                    $('#boiler').parent().parent().parent().hide();

                } else {
                    //$('#boiler').parent().show();
                    // Hide the interest input
                    //$('#boiler').parent().parent().next().show();
                    $('#boiler').parent().parent().parent().show();
                }
            });

            $("#total-sun-panels").trigger('change');


            // Firefox fix, who else thinks that stuff has changed
            document.getElementById("main-form").reset();
            $('#main-form').trigger('reinitialize.areYouSure');
            // End Firefox fix
        });
    </script>
@endpush