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
                        <div class="form-group add-space{{ $errors->has('example_building_id') ? ' has-error' : '' }}">
                            <label for="example_building_id" class=" control-label"><i data-toggle="modal" data-target="#example-building-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>{{ \App\Helpers\Translation::translate('general-data.example-building.title') }}</label>
                            <select id="example_building_id" class="form-control" name="example_building_id" data-ays-ignore="true"> {{-- data-ays-ignore="true" makes sure this field is not picked up by Are You Sure --}}
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
                                @if(empty(old('example_building_id', $building->example_building_id)) || $currentNotInExampleBuildings) selected="selected"@endif >@lang('woningdossier.cooperation.tool.general-data.example-building.no-match')</option>
                            </select>

                            @if ($errors->has('example_building_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('example_building_id') }}</strong>
                                </span>
                            @endif

                            @component('cooperation.tool.components.help-modal')
                                {{ \App\Helpers\Translation::translate('general-data.example-building.help') }}
                            @endcomponent
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('surface') ? ' has-error' : '' }}">
                            <label for="surface" class=" control-label">
                                <i data-toggle="modal" data-target="#user-surface-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                   aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.building-type.what-user-surface.title')}}
                            </label> <span>*</span>

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'surface', 'needsFormat' => true])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                                <input id="surface" type="text" class="form-control" name="surface"
                                       value="{{ old('surface', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'surface'), 1)) }}"
                                       required autofocus>
                            @endcomponent

                            @component('cooperation.tool.components.help-modal')
                                {{\App\Helpers\Translation::translate('general-data.building-type.what-user-surface.help')}}
                            @endcomponent

                            @if ($errors->has('surface'))
                                <span class="help-block">
                                <strong>{{ $errors->first('surface') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('building_layers') ? ' has-error' : '' }}">
                            <label for="building_layers" class=" control-label">
                                <i data-toggle="modal" data-target="#roof-layers-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false">
                                </i>{{\App\Helpers\Translation::translate('general-data.building-type.how-much-building-layers.title')}}
                            </label>

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'building_layers', 'needsFormat' => true, 'decimals' => 0])
                                <input id="building_layers" type="text" class="form-control" name="building_layers"
                                       value="{{ old('building_layers', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_layers')) }}" autofocus>
                            @endcomponent



                            @component('cooperation.tool.components.help-modal')
                                {{\App\Helpers\Translation::translate('general-data.building-type.how-much-building-layers.help')}}
                            @endcomponent

                            @if ($errors->has('building_layers'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('building_layers') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('roof_type_id') ? ' has-error' : '' }}">
                            <label for="roof_type_id" class=" control-label">
                                <i data-toggle="modal" data-target="#roof-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('general-data.building-type.type-roof.title')}}
                            </label>

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


                            @component('cooperation.tool.components.help-modal')
                                {{\App\Helpers\Translation::translate('general-data.building-type.type-roof.help')}}
                            @endcomponent

                            @if ($errors->has('roof_type_id'))
                                <span class="help-block">
                                <strong>{{ $errors->first('roof_type_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('energy_label_id') ? ' has-error' : '' }}">
                            <label for="energy_label_id" class=" control-label">
                                <i data-toggle="modal" data-target="#current-energy-label-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('general-data.building-type.current-energy-label.title')}}
                            </label>

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

                            @component('cooperation.tool.components.help-modal')
                                {{\App\Helpers\Translation::translate('general-data.building-type.current-energy-label.help')}}
                            @endcomponent

                            @if ($errors->has('energy_label_id'))
                                <span class="help-block">
                                <strong>{{ $errors->first('energy_label_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group input-source-group">
                            <div class="form-group add-space{{ $errors->has('monument') ? ' has-error' : '' }}">
                                <label for="monument" class=" control-label">
                                <i data-toggle="modal" data-target="#is-monument-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('general-data.building-type.is-monument.title')}}
                            </label>
	                            <?php $checked = (int) old('monument', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'monument')); ?>
                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="1"
                                       @if($checked == 1) checked @endif>{{\App\Helpers\Translation::translate('general.options.radio.yes.title')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="2"
                                       @if($checked == 2) checked @endif>{{\App\Helpers\Translation::translate('general.options.radio.no.title')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="0"
                                       @if($checked == 0) checked @endif>{{\App\Helpers\Translation::translate('general.options.radio.unknown.title')}}
                                </label>

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.building-type.is-monument.help')}}
                                @endcomponent

                                @if ($errors->has('monument'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('monument') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                <ul class="dropdown-menu">
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

                                        <li class="change-input-value" data-input-source-short="{{$userInputValue->inputSource()->first()->short}}" data-input-value="{{ $value }}"><a href="#">{{ $userInputValue->getInputSourceName() }}: {{ $trans }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div id="energy-saving-measures">
            @include('cooperation.layouts.section-title', [
                'translationKey' => 'general-data.energy-saving-measures.title',
                'infoAlertId' => 'energy-saving-measures-info'
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
                                    {{ $element->name }}
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $element->values()->orderBy('order')->get(), 'userInputValues' => $building->buildingElements()->forMe()->where('element_id', $element->id)->get(), 'userInputColumn' => 'element_value_id'])
                                    <select id="element_{{ $element->id }}" class="form-control"
                                            name="element[{{ $element->id }}]">
                                        @foreach($element->values()->orderBy('order')->get() as $elementValue)
                                            {{--<option @if($elementValue->id == old('element.'.$element->id.'')) selected="selected"
                                                    @elseif($building->buildingElements()->where('element_id', $element->id)->first() && $building->buildingElements()->where('element_id', $element->id)->first()->element_value_id == $elementValue->id) selected
                                                    @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option> --}}
                                            <option @if(old('element.' . $element->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $element->id), 'element_value_id')) == $elementValue->id) selected="selected" @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                                        @endforeach
                                    </select>
                                @endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    {{ $element->info }}
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
                                <div class="form-group add-space{{ $errors->has('user_interest.element.' . $element->id) ? ' has-error' : '' }}">
                                    <label for="element_interested_{{ $element->id }}-info" class="control-label small-text" style="font-size:12px;">
                                        <i data-toggle="modal" data-target="#element_interested_{{ $element->id }}-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                        {{\App\Helpers\Translation::translate('general.interested-in-improvement.title')}}
                                        <span>*</span>
                                    </label>
                                @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', 'element')->where('interested_in_id', $element->id),  'userInputColumn' => 'interest_id'])
                                <select id="user_interest_element_{{ $element->id }}" class="form-control" name="user_interest[element][{{ $element->id }}]" >
                                    @foreach($interests as $interest)
                                        <option @if($interest->id == old('user_interest.element.'. $element->id, \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->interests()->where('interested_in_type', 'element')->where('interested_in_id', $element->id), 'interest_id'))) selected="selected" @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                    @endforeach
                                </select>
                                @endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    {{ \App\Helpers\Translation::translate('general.interested-in-improvement.help') }}
                                @endcomponent

                                    @if ($errors->has('user_interest.element.' . $element->id))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user_interest.element.' . $element->id) }}</strong>
                                    </span>
                                    @endif
                                </div>
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
            /** @var \App\Models\UserInterest|null $serviceInterestForCurrentUser holds the interest for the current inputsource for a resident */
            $serviceInterestForCurrentUser = $userInterestsForMe
                ->where('interested_in_type', 'service')
                ->where('input_source_id', \App\Helpers\HoomdossierSession::getInputSource())
                ->where('interested_in_id', $service->id)
                ->first();
            ?>
                @if ( ($i % 2 == 0 && $service->short != "boiler") || $service->short == 'total-sun-panels')
                <div class="row" id="service_row_{{$service->id}}">
            @elseif(strpos($service->name, 'geventileerd'))
                </div><div class="row">
            @endif
            @if($service->short == "hr-boiler")
                <div class="row">
            @endif
                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('service.'.$service->id) ? ' has-error' : '' }}">
                        <label for="{{ $service->short }}" class="control-label">
                            <i data-toggle="modal" data-target="#service_{{ $service->id }}-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                            {{ $service->name }}
                        </label>
                        {{-- This will check if the service has values. If so we need a selectbox. If not: a textbox --}}
                        @if($service->values()->where('service_id', $service->id)->first() != null)

                            <?php
                                $selectedSV = old('service.'.$service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $service->id), 'service_value_id'));
                                /*if (is_null($selectedSV)){
                                	$buildServ = $building->buildingServices()->where('service_id', $service->id)->first();
                                	if ($buildServ instanceof \App\Models\BuildingService){
                                		$selectedSV = $buildServ->service_value_id;
                                    }
                                }*/
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
                                <select id="{{ $service->short }}" class="form-control" name="service[{{ $service->id }}]">
                                    @foreach($service->values()->orderBy('order')->get() as $serviceValue)
                                        <option @if($serviceValue->id == $selectedSV) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                                    {{--<option @if(old('service.'.$service->id) == $serviceValue->id) selected="selected" @elseif($building->buildingServices()->where('service_id', $service->id)->first() != null && $building->buildingServices()->where('service_id', $service->id)->first()->service_value_id == $serviceValue->id) selected @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>--}}
                                    @endforeach
                                </select>
                            @endcomponent
                        @else
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $building->buildingServices()->forMe()->where('service_id', $service->id)->get(),'userInputColumn' => 'extra.value'])
                                <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.pieces')</span>
                                <input type="text" id="{{ $service->short }}" class="form-control" value="{{ old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $service->id), 'extra.value')) }}" name="service[{{ $service->id }}]">
                                {{--<input type="text" id="{{ $service->short }}" class="form-control" value="@if(old('service.' . $service->id )){{old('service.' . $service->id)}} @elseif(isset($building->buildingServices()->where('service_id', $service->id)->first()->extra['value'])){{$building->buildingServices()->where('service_id', $service->id)->first()->extra['value']}} @endif" name="service[{{ $service->id }}]">--}}
                            @endcomponent
                        @endif

                        @component('cooperation.tool.components.help-modal')
                            {{ $service->info }}
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
                    <div class="form-group add-space{{ $errors->has('user_interest.service.' . $service->id) ? ' has-error' : '' }}">
                        <label for="user_interest_service_{{ $service->id }}" class="control-label small-text" style="font-size:12px;">
                            <i data-toggle="modal" data-target="#service_interested_{{ $service->id }}-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                            {{ \App\Helpers\Translation::translate('general.interested-in-improvement.title') }}
                        </label> <span>*</span>
                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', 'service')->where('interested_in_id', $service->id),  'userInputColumn' => 'interest_id'])
                        <select id="user_interest_service_{{ $service->id }}" class="form-control" name="user_interest[service][{{ $service->id }}]" >
                            @foreach($interests as $interest)
                                <option @if($interest->id == old('user_interest.service.'. $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->interests()->where('interested_in_type', 'service')->where('interested_in_id', $service->id), 'interest_id'))) selected="selected" @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                {{--
                                <option @if($interest->id == old('user_interest.service.' . $service->id )) selected
                                        @elseif($serviceInterestForCurrentUser instanceof \App\Models\UserInterest &&
                                        $serviceInterestForCurrentUser->interest_id == $interest->id) selected @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                --}}
                                            @endforeach
                                        </select>@endcomponent
                        @component('cooperation.tool.components.help-modal')
                            {{ \App\Helpers\Translation::translate('general.interested-in-improvement.help') }}
                        @endcomponent


                        @if ($errors->has('user_interest.service.' . $service->id))
                            <span class="help-block">
                            <strong>{{ $errors->first('user_interest.service.' . $service->id) }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @endif

                            @if(strpos($service->name, 'geventileerd') || $service->short == "total-sun-panels")
                                <div class="col-sm-6 {{ $errors->has(''.$service->id.'.extra') ? ' show' : '' }}">

                                    <div id="{{$service->id.'-extra'}}"
                             class="form-group add-space{{ $errors->has(''.$service->id.'.extra') ? ' has-error' : '' }}">
                            <label for="service_{{ $service->id }}" class="control-label">
                                <i data-toggle="modal"
                                   data-target="#service_{{ $service->id }}-extra-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                   aria-expanded="false"></i>
                                @if(strpos($service->name, 'geventileerd'))
                                    {{\App\Helpers\Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.title')}}
                                @elseif($service->short == 'total-sun-panels')
                                    {{\App\Helpers\Translation::translate('general-data.energy-saving-measures.solar-panels.if-yes.title')}}
                                @endif

                            </label>

                                        <?php
                                        if (isset($building->buildingServices()->where('service_id', $service->id)->first()->extra['year'])) {
                                            $year = $building->buildingServices()->where('service_id', $service->id)->first()->extra['year'];
                                        }
                                        ?>

                                        @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'input', 'userInputValues' => $building->buildingServices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'extra.year'])
                                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                                <input type="text" class="form-control" name="{{ $service->id }}[extra][year]" value="{{ old($service->id . '.extra.year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $service->id), 'extra.year')) }}">
                                {{--<input type="text" class="form-control" name="{{ $service->id }}[extra][year]" value="@if(old($service->id.'.extra.year')){{ old($service->id.'.extra.year') }}@elseif(isset($year)){{ $year }}@endif">--}}
                                        @endcomponent

                            @component('cooperation.tool.components.help-modal')
                                {{\App\Helpers\Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.help')}}
                            @endcomponent

                                        @if ($errors->has($service->id.'.extra'))
                                            <span class="help-block">
                                    <strong>{{ $errors->first($service->id.'.extra.year') }}</strong>
                                </span>
                                        @endif
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
                @include('cooperation.layouts.section-title', [
                    'translationKey' => 'general-data.data-about-usage.title',
                    'infoAlertId' => 'data-about-usage-info'
                ])
            <div class="row">
                <div class="col-sm-6">

                    <div class="form-group add-space{{ $errors->has('resident_count') ? ' has-error' : '' }}">
                        <label for="resident_count" class=" control-label"><i data-toggle="modal" data-target="#resident_count-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.total-citizens.title')}}
                                </label> <span>*</span>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'resident_count'])
                            <input type="text" id="resident_count" class="form-control" value="{{ old('resident_count', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'resident_count')) }}" name="resident_count" required>
                        {{--<input type="text" id="resident_count" class="form-control" value="@if(old('resident_count') != ""){{old('resident_count')}}@elseif(isset($energyHabit)){{$energyHabit->resident_count}}@endif" name="resident_count" required>--}}
                        @endcomponent
                    @component('cooperation.tool.components.help-modal')
                        {{\App\Helpers\Translation::translate('general-data.data-about-usage.total-citizens.help')}}
                    @endcomponent

                        @if ($errors->has('resident_count'))
                            <span class="help-block">
                        <strong>{{ $errors->first('resident_count') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

                <div class="col-sm-6">

                    <div class="form-group add-space{{ $errors->has('cook_gas') ? ' has-error' : '' }}">
                        <label for="cook_gas" class=" control-label">
                            <i data-toggle="modal" data-target="#cooked-on-gas-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                            {{\App\Helpers\Translation::translate('general-data.data-about-usage.cooked-on-gas.title')}}
                        </label>
                        <span>*</span>

                        <div class="input-group input-source-group">
                            <br>
                            <label class="radio-inline">
                                <input type="radio" name="cook_gas" @if(old('cook_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'cook_gas')) == 1) checked @endif value="1">{{\App\Helpers\Translation::translate('general.options.radio.yes.title')}}
                                {{--<input type="radio" name="cook_gas" @if(old('cook_gas') == 1) checked @elseif(isset($energyHabit) && $energyHabit->cook_gas == 1) checked @endif  value="1">@lang('woningdossier.cooperation.radiobutton.yes')--}}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="cook_gas" @if(old('cook_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'cook_gas')) == 2) checked @endif value="2">{{\App\Helpers\Translation::translate('general.options.radio.no.title')}}
                            </label>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                <ul class="dropdown-menu">
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
                                        <li class="change-input-value" data-input-source-short="{{$userInputValue->inputSource()->first()->short}}" data-input-value="{{ $value }}"><a href="#">{{ $userInputValue->getInputSourceName() }}: {{ $trans }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        @component('cooperation.tool.components.help-modal')
                        {{\App\Helpers\Translation::translate('general-data.data-about-usage.cooked-on-gas.help')}}
                        @endcomponent

                                @if ($errors->has('cook_gas'))
                                    <span class="help-block">
                            <strong>{{ $errors->first('cook_gas') }}</strong>
                        </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('thermostat_high') ? ' has-error' : '' }}">
                                <label for="thermostat_high" class=" control-label">
                                    <i data-toggle="modal" data-target="#thermostat-high-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.thermostat-highest.title')}}
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'thermostat_high', 'needsFormat' => true])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.degrees.title')}}</span>
                            <input type="text" id="thermostat_high" class="form-control" value="{{ old('thermostat_high', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'thermostat_high', 20), 1)) }}" name="thermostat_high">
                            {{--<input type="text" id="thermostat_high" class="form-control" value="@if(!empty(old('thermostat_high'))){{ old('thermostat_high', 20) }}@elseif(isset($energyHabit)){{ \App\Helpers\NumberFormatter::format($energyHabit->thermostat_high, 1) }}@else{{ \App\Helpers\NumberFormatter::format(20, 1) }}@endif"
                                           name="thermostat_high">--}}
                                @endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.thermostat-highest.help')}}
                                @endcomponent

                                @if ($errors->has('thermostat_high'))
                                    <span class="help-block">
                        <strong>{{ $errors->first('thermostat_high') }}</strong>
                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('thermostat_low') ? ' has-error' : '' }}">
                                <label for="thermostat_low" class=" control-label"><i data-toggle="modal"
                                                                                      data-target="#thermostat-low-info"
                                                                                      class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                                                                      aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.thermostat-lowest.title')}}
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'thermostat_low', 'needsFormat' => true])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.degrees.title')}}</span>
                            <input id="thermostat_low" type="text" class="form-control" name="thermostat_low" value="{{ old('thermostat_low', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'thermostat_low', 16), 1)) }}">
                            {{--<input id="thermostat_low" type="text" class="form-control" name="thermostat_low" value="@if(!empty(old('thermostat_low'))){{ old('thermostat_low', 16) }}@elseif(isset($energyHabit)){{ \App\Helpers\NumberFormatter::format($energyHabit->thermostat_low, 1) }}@else{{ \App\Helpers\NumberFormatter::format(16, 1) }}@endif">--}}
                                @endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.thermostat-lowest.help')}}
                                @endcomponent

                                @if ($errors->has('thermostat_low'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('thermostat_low') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('hours_high') ? ' has-error' : '' }}">
                                <label for="hours_high" class=" control-label">
                                    <i data-toggle="modal" data-target="#hours-high-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.max-hours-thermostat-highest.title')}}
                                </label>


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
                                    <option @if($hour === $selectedHours) selected @endif value="{{ $hour }}">{{ $hour }}</option>
                                    {{--<option @if($hour === old('hours_high')) selected @elseif(isset($energyHabit) && $energyHabit->hours_high == $hour) selected @elseif(!isset($energyHabit) && $hour == 12) selected @endif value="{{ $hour }}">{{ $hour }}</option>--}}
                                @endforeach
                                    <option @if(0 === $selectedHours) selected @endif value="0">
                                {{--<option @if($hour === old('hours_high')) selected @elseif(isset($energyHabit) && $energyHabit->hours_high == 0) selected @endif value="0">--}}
                                    @lang('woningdossier.cooperation.radiobutton.not-important')
                                </option>
                            </select>
                        @endcomponent
                                @component('cooperation.tool.components.help-modal')
                            {{ \App\Helpers\Translation::translate('general-data.data-about-usage.max-hours-thermostat-highest.help') }}
                                @endcomponent
                        @if ($errors->has('hours_high'))
                            <span class="help-block">
                                <strong>{{ $errors->first('hours_high') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('heating_first_floor') ? ' has-error' : '' }}">
                                <label for="heating_first_floor" class=" control-label">
                                    <i data-toggle="modal" data-target="#heating-first-floor-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.situation-first-floor.title')}}
                                </label>

                                <?php

                                $bhDefault = $buildingHeatings->where('is_default', '=', true)->first();
                            if ($bhDefault instanceof \App\Models\BuildingHeating) {
                                $defaultHFF = $bhDefault->id;
                            }

                            $selectedHFF = old('heating_first_floor', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'heating_first_floor', $defaultHFF));

                            /*
                            $selectedHFF = old('heating_first_floor', null);
                                $selectedHFFColumn = 'heating_first_floor';if (is_null($selectedHFF)) {
                                    if (isset($energyHabit)) {
                                        $selectedHFFColumn = 'heating_first_floor';
                                    $selectedHFF = $energyHabit->heating_first_floor;
                                    }
                                }
                                if (is_null($selectedHFF)) {
                                    $selectedHeating = $buildingHeatings->where('is_default', '=', true)->first();
                                    if ($selectedHeating instanceof \App\Models\BuildingHeating) {
                                        $selectedHFFColumn = 'id';
                                        $selectedHFF = $selectedHeating->id;
                                    }
                                }
                            */
                                ?>

                                @component('cooperation.tool.components.input-group',['inputType' => 'select', 'inputValues' => $buildingHeatings, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'heating_first_floor'])<select id="heating_first_floor" class="form-control" name="heating_first_floor">
                                    @foreach($buildingHeatings as $buildingHeating)
                                        <option @if($buildingHeating->id == $selectedHFF) selected="selected"
                                                @endif value="{{ $buildingHeating->id}}">{{ $buildingHeating->name }}</option>
                                    @endforeach
                                </select>@endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.situation-first-floor.help')}}
                                @endcomponent

                                @if ($errors->has('heating_first_floor'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('heating_first_floor') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('heating_second_floor') ? ' has-error' : '' }}">
                                <label for="heating_second_floor" class=" control-label">
                                    <i data-toggle="modal"
                                       data-target="#heating-second-floor-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.situation-second-floor.title')}}
                                </label>

                                <?php

                                $bhDefault = $buildingHeatings->where('is_default', '=', true)->first();
                        if ($bhDefault instanceof \App\Models\BuildingHeating) {
                            $defaultHSF = $bhDefault->id;
                        }

                        $selectedHSF = old('heating_second_floor', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'heating_second_floor', $defaultHSF));

                        /*
                    $selectedHSF = old('heating_second_floor', null);
                    $selectedHSFColumn = 'heating_second_floor';
                    if (is_null($selectedHSF)){
                        if(isset($energyHabit)){
                            $selectedHSFColumn = 'heating_second_floor';
                            $selectedHSF = $energyHabit->heating_second_floor;
                        }
                    }
                    if (is_null($selectedHSF)){
                        $selectedHeating = $buildingHeatings->where('is_default', '=', true)->first();
                        if ($selectedHeating instanceof \App\Models\BuildingHeating){
                            $selectedHSFColumn = 'id';
                            $selectedHSF = $selectedHeating->id;
                        }
                    }
                        */

                                ?>

                            @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $buildingHeatings, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'heating_second_floor'])
                            <select id="heating_second_floor" class="form-control" name="heating_second_floor">
                                @foreach($buildingHeatings as $buildingHeating)
                                    <option @if($buildingHeating->id == $selectedHSF) selected="selected"
                                            @endif value="{{ $buildingHeating->id }}">{{ $buildingHeating->name }}</option>
                                @endforeach
                            </select>
                            @endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.situation-second-floor.help')}}
                                @endcomponent

                                @if ($errors->has('heating_second_floor'))
                                    <span class="help-block">
                        <strong>{{ $errors->first('heating_second_floor') }}</strong>
                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('water_comfort') ? ' has-error' : '' }}">
                                <label for="water_comfort" class=" control-label">
                                    <i data-toggle="modal"
                                       data-target="#comfortniveau-warm-tapwater-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.comfortniveau-warm-tapwater.title')}}
                                </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $comfortLevelsTapWater, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'water_comfort_id'])<select id="water_comfort" class="form-control" name="water_comfort" >
                            @foreach($comfortLevelsTapWater as $comfortLevelTapWater)
                                <option @if(old('water_comfort', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'water_comfort_id')) == $comfortLevelTapWater->id) selected="selected" @endif value="{{ $comfortLevelTapWater->id }}">{{ $comfortLevelTapWater->name }}</option>
                                    {{--<option @if($comfortLevelTapWater->id == old('water_comfort')) selected @elseif(isset($energyHabit) && $energyHabit->water_comfort_id == $comfortLevelTapWater->id) selected @endif value="{{ $comfortLevelTapWater->id }}">{{ $comfortLevelTapWater->name }}</option>--}}
                            @endforeach
                        </select>@endcomponent
                                @component('cooperation.tool.components.help-modal')
                            {{\App\Helpers\Translation::translate('general-data.data-about-usage.comfortniveau-warm-tapwater.help')}}
                                @endcomponent

                                @if ($errors->has('water_comfort'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('water_comfort') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('amount_electricity') ? ' has-error' : '' }}">
                                <label for="amount_electricity" class=" control-label">
                                    <i data-toggle="modal"
                                       data-target="#amount-electricity-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.electricity-consumption-past-year.title')}} <span>*</span></label>

                                @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'amount_electricity'])
                                    <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.kwh')</span>
                                    <input id="amount_electricity" type="text" value="{{ old('amount_electricity', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'amount_electricity')) }}" class="form-control" name="amount_electricity" required>
                            {{--<input id="amount_electricity" type="text"
                                           value="@if(old('amount_electricity') != ""){{ old('amount_electricity') }}@elseif(isset($energyHabit)){{ $energyHabit->amount_electricity }}@endif"
                                           class="form-control" name="amount_electricity" required>--}}
                                @endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.electricity-consumption-past-year.help')}}
                                @endcomponent

                                @if ($errors->has('amount_electricity'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('amount_electricity') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('amount_gas') ? ' has-error' : '' }}">
                                <label for="amount_gas" class=" control-label">
                                    <i data-toggle="modal"
                                       data-target="#amount-gas-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.gas-usage-past-year.title')}}
                                    <span>*</span></label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'amount_gas'])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.cubic-meters.title')}}</span>
                            <input id="amount_gas" type="text" value="{{ old('amount_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'amount_gas')) }}" class="form-control" name="amount_gas" required>
                            {{--<input id="amount_gas" type="text" value="@if(old('amount_gas') != ""){{ old('amount_gas') }}@elseif(isset($energyHabit)){{ $energyHabit->amount_gas }}@endif"
                                           class="form-control" name="amount_gas" required>--}}
                                @endcomponent

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.gas-usage-past-year.help')}}
                                @endcomponent


                                @if ($errors->has('amount_gas'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('amount_gas') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group add-space{{ $errors->has('living_situation_extra') ? ' has-error' : '' }}">
                                <label for="additional-info" class=" control-label">
                                    <i data-toggle="modal"
                                       data-target="#living-situation-extra-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.additional-info.title')}}</label>
                                        <textarea id="additional-info" class="form-control" name="living_situation_extra">{{ old('living_situation_extra', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'living_situation_extra')) }}</textarea>

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.additional-info.help')}}
                                @endcomponent

                                @if ($errors->has('living_situation_extra'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('living_situation_extra') }}</strong>
                            </span>
                        @endif
                    </div>
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
                            @include('cooperation.layouts.section-title', [
                                'translationKey' => 'general-data.motivation.title',
                                'infoAlertId' => 'motivation-info'
                            ])
                        </div>

                        {{-- Start at 1 so the translation will too. --}}
                        @for($i = 1; $i < 5; $i++)
                            <div class="col-sm-6">
                                <div class="form-group add-space{{ $errors->has('motivation.'.$i) ? ' has-error' : '' }}">
                                    <label for="motivation[{{ $i }}]" class=" control-label">
                                        <i data-toggle="modal"
                                           data-target="#motivation-{{ $i }}-info"
                                           class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                           aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.motivation.priority.title', ['prio' => $i])}}
                                    </label>

                                    <select id="motivation[{{ $i }}]" class="form-control" name="motivation[{{ $i }}]">

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
                                    @component('cooperation.tool.components.help-modal')
                                        {{\App\Helpers\Translation::translate('general-data.motivation.priority.help')}}
                                    @endcomponent

                                    @if ($errors->has('motivation.'.$i))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('motivation.'.$i) }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group add-space{{ $errors->has('motivation_extra') ? ' has-error' : '' }}">
                                <label for="motivation-extra" class=" control-label"><i data-toggle="modal"
                                                                                        data-target="#motivation-extra-info"
                                                                                        class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                                                                        aria-expanded="false"></i>
                                    {{\App\Helpers\Translation::translate('general-data.motivation-extra.title')}}
                                </label>

                                <textarea id="motivation-extra" class="form-control"
                                          name="motivation_extra">@if(old('motivation_extra') != ""){{ old('motivation_extra') }}@elseif(isset($energyHabit)){{ $energyHabit->motivation_extra }}@endif</textarea>

                                @component('cooperation.tool.components.help-modal')
                                    {{\App\Helpers\Translation::translate('general-data.motivation-extra.help')}}
                                @endcomponent

                                @if ($errors->has('motivation_extra'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('motivation_extra') }}</strong>
                            </span>
                                @endif
                            </div>
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

    </form>
@endsection

@push('js')
    <script>

        $(document).ready(function () {

            $(window).keydown(function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

            var previous_eb = parseInt('{{ $building->example_building_id }}');

            $("select#example_building_id").change(function() {
                // Do something with the previous value after the change
                if (this.value !== previous_eb ){
                    if (previous_eb === "" || confirm('@lang('woningdossier.cooperation.tool.general-data.example-building.apply-are-you-sure')')) {
                        @if(App::environment('local'))
                        console.log("Let's save it. EB id: " + this.value);
                        @endif

                        // Firefox fix, who else thinks that stuff has changed
                        $(this).closest('form').trigger('reinitialize.areYouSure');
                        // End Firefox fix

                        $.ajax({
                            type: "POST",
                            url: '{{ route('cooperation.tool.apply-example-building', [ 'cooperation' => $cooperation ]) }}',
                            data: { example_building_id: this.value },
                            success: function(data){
                                location.reload();
                            }
                        });


                        // Make sure the previous value is updated
                        previous_eb = this.value;
                    } else {
                        if ( $("#select-box option[value='" + previous_eb + "']").val() === undefined) {
                            @if(App::environment('local'))
                            console.log("Prev: " + previous_eb + " does not exist. Setting to empty");
                            @endif
                            $(this).val("");
                        }
                        else {
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
                    if($('#house-ventilation').parent().parent().parent().next().next().find('input').val().trim() !== "") {
                        console.log("Adjusting house ventilation");
                        $('#house-ventilation').parent().parent().parent().next().next().find('input').val("");
                    }
                }
            });

            // check if a user is interested in a sun panel
            $(document).change('#total-sun-panels', function () {
                var input = $("#total-sun-panels");
                var totalSunPanels = parseInt(input.val());
                var extraField =  input.parent().parent().parent().next().next();

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