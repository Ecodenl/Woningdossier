@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.general-data.title'))

@section('step_content')

    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.general-data.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div class="row">
            <div id="building-type" class="col-md-12">
                <h4 style="margin-left: -5px;">{{\App\Helpers\Translation::translate('general-data.building-type.title.title')}} </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('building_type_id') ? ' has-error' : '' }}">
                            <label for="building_type_id" class=" control-label">
                                <i data-toggle="collapse" data-target="#building-type-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                   aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('general-data.building-type.what-type.title')}}
                            </label>

                            @component('cooperation.tool.components.input-group', [
                                'inputType' => 'select',
                                'inputValues' => $buildingTypes,
                                'userInputValues' => $building->buildingFeatures()->forMe()->get(),
                                'userInputModel' => 'buildingType',
                                'userInputColumn' => 'building_type_id'
                            ])
                                <select id="building_type_id" class="form-control" name="building_type_id">
                                    @foreach($buildingTypes as $buildingType)
                                        <option @if(old('building_type_id') && $buildingType->id == old('building_type_id'))
                                                selected="selected"
                                                @elseif(isset($building->buildingFeatures->buildingType) && $building->buildingFeatures->buildingType->id == $buildingType->id)
                                                selected="selected"
                                                @endif value="{{ $buildingType->id }}">{{ $buildingType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endcomponent

                            <div id="building-type-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('general-data.building-type.what-type.help')}}
                            </div>
                            @if ($errors->has('building_type_id'))
                                <span class="help-block">
                                <strong>{{ $errors->first('building_type_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('build_year') ? ' has-error' : '' }}">
                            <label for="build_year" class=" control-label">
                                <i data-toggle="collapse" data-target="#what-building-year-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('general-data.building-type.what-building-year.title')}}
                                <span>*</span>
                            </label>

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'build_year'])
                                <input id="build_year" type="text" class="form-control" name="build_year"
                                       value="@if(isset($building->buildingFeatures->build_year)){{ old('build_year', $building->buildingFeatures->build_year) }}@else{{ old('build_year') }}@endif"
                                       required autofocus>
                            @endcomponent

                            <div id="what-building-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('general-data.building-type.what-building-year.help')}}
                            </div>

                            @if ($errors->has('build_year'))
                                <span class="help-block">
                                <strong>{{ $errors->first('build_year') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div id="example-building" class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('example_building_id') ? ' has-error' : '' }}">
                            <label for="example_building_id" class=" control-label"><i data-toggle="collapse" data-target="#example-building-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.example-building-type')</label>
                            <select id="example_building_id" class="form-control" name="example_building_id" data-ays-ignore="true"> {{-- data-ays-ignore="true" makes sure this field is not picked up by Are You Sure --}}
                                @foreach($exampleBuildings as $exampleBuilding)
                                    <option @if(is_null(old('example_building_id')) && is_null($building->example_building_id) && !Auth::user()->hasCompleted($step) && $exampleBuilding->is_default)
                                            selected="selected"
                                            @elseif($exampleBuilding->id == old('example_building_id'))
                                            selected="selected"
                                            @elseif ($building->example_building_id == $exampleBuilding->id)
                                                    selected="selected"
                                            @endif
                                            value="{{ $exampleBuilding->id }}">{{ $exampleBuilding->name }}</option>
                                @endforeach
                                <option value="" @if(empty(old('example_building_id', $building->example_building_id)) && Auth::user()->hasCompleted($step))selected="selected"@endif >@lang('woningdossier.cooperation.tool.general-data.example-building.no-match')</option>
                            </select>

                            @if ($errors->has('example_building_id'))
                                <span class="help-block">
                    <strong>{{ $errors->first('example_building_id') }}</strong>
                </span>
                            @endif
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group add-space">
                                <div id="example-building-info" class="collapse alert alert-info remove-collapse-space">
                                    I would like to have some help full information right here!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('surface') ? ' has-error' : '' }}">
                            <label for="surface" class=" control-label">
                                <i data-toggle="collapse" data-target="#user-surface-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                   aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.building-type.what-user-surface.title')}}
                            </label> <span>*</span>

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'surface', 'needsFormat' => true])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                                <input id="surface" type="text" class="form-control" name="surface"
                                       value="@if(old('surface')){{ old('surface') }}@elseif(isset($building->buildingFeatures)){{ \App\Helpers\NumberFormatter::format($building->buildingFeatures->surface, 1) }}@endif"
                                       required autofocus>
                            @endcomponent

                            <div id="user-surface-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('general-data.building-type.what-user-surface.help')}}
                            </div>

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
                                <i data-toggle="collapse" data-target="#roof-layers-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false">
                                </i>{{\App\Helpers\Translation::translate('general-data.building-type.how-much-building-layers.title')}}
                            </label>

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'building_layers', 'needsFormat' => true])
                                <input id="building_layers" type="text" class="form-control" name="building_layers"
                                       value="@if(isset($building->buildingFeatures->building_layers)){{ old('building_layers', $building->buildingFeatures->building_layers) }}@else{{ old('building_layers') }}@endif"
                                       req autofocus>
                            @endcomponent

                            <div id="roof-layers-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('general-data.building-type.how-much-building-layers.help')}}
                            </div>

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
                                <i data-toggle="collapse" data-target="#roof-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('general-data.building-type.type-roof.title')}}
                            </label>

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $roofTypes, 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputModel' => 'roofType', 'userInputColumn' => 'roof_type_id'])
                                <select id="roof_type_id" class="form-control" name="roof_type_id">
                                    @foreach($roofTypes as $roofType)
                                        <option
                                                @if(old('roof_type_id') && $roofType->id == old('roof_type_id'))
                                                selected="selected"
                                                @elseif(isset($building->buildingFeatures->roofType) && $building->buildingFeatures->roofType->id == $roofType->id)
                                                selected="selected"
                                                @endif
                                                value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                    @endforeach
                                </select>
                            @endcomponent

                            <div id="roof-type-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('general-data.building-type.type-roof.help')}}
                            </div>

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
                                <i data-toggle="collapse" data-target="#current-energy-label-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('general-data.building-type.current-energy-label.title')}}
                            </label>

                            <?php

                            // order:
                            //  1) old value
                            //  2) db value
                            //  3) default (G)

                            $selected = old('energy_label_id');

                            if (is_null($selected)) {
                                if (isset($building->buildingFeatures->energyLabel) && $building->buildingFeatures->energyLabel instanceof \App\Models\EnergyLabel) {
                                    $selected = $building->buildingFeatures->energyLabel->id;
                                }
                            }

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
                            <div id="current-energy-label-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('general-data.building-type.current-energy-label.help')}}
                            </div>

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
                                <i data-toggle="collapse" data-target="#is-monument-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('general-data.building-type.is-monument.title')}}
                            </label>

                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="1"
                                       @if(isset($building->buildingFeatures->monument) && $building->buildingFeatures->monument == 1) checked
                                       @elseif(old('monument') == 1) checked @endif>{{\App\Helpers\Translation::translate('general.options.radio.yes.title')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="2"
                                       @if(isset($building->buildingFeatures->monument) && $building->buildingFeatures->monument == 2) checked
                                       @elseif(old('monument') == 2) checked @endif>{{\App\Helpers\Translation::translate('general.options.radio.no.title')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="monument" value="0"
                                       @if(isset($building->buildingFeatures->monument) && $building->buildingFeatures->monument == "0") checked
                                       @elseif(old('monument') == "0") checked @endif>{{\App\Helpers\Translation::translate('general.options.radio.unknown.title')}}
                                </label>

                                <div id="is-monument-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.building-type.is-monument.help')}}
                                </div>

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
                                        if ($value === 1) {
                                            $trans = __('woningdossier.cooperation.radiobutton.yes');
                                        } elseif($value === 2) {
                                            $trans = __('woningdossier.cooperation.radiobutton.no');
                                        } else {
                                            $trans = __('woningdossier.cooperation.radiobutton.unknown');
                                        }
                                        ?>

                                        <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$trans}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div id="energy-saving-measures">
            <h4 style="margin-left: -5px;">{{\App\Helpers\Translation::translate('general-data.energy-saving-measures.title.title')}} </h4>

            @foreach($elements as $i => $element)
                @if ($i % 2 == 0)
                    <div class="row">
                        @endif
                        <div class="@if(in_array($element->short, ['sleeping-rooms-windows', 'living-rooms-windows'])) col-sm-6 @else col-sm-4 @endif">
                            <div class="form-group add-space{{ $errors->has('element.'.$element->id) ? ' has-error' : '' }}">
                                <label for="element_{{ $element->id }}" class="control-label">
                                    <i data-toggle="collapse" data-target="#element_{{ $element->id }}-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>
                                    {{ $element->name }}
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $element->values()->orderBy('order')->get(), 'userInputValues' => $building->buildingElements()->forMe()->where('element_id', $element->id)->get(), 'userInputColumn' => 'element_value_id'])
                                    <select id="element_{{ $element->id }}" class="form-control"
                                            name="element[{{ $element->id }}]">
                                        @foreach($element->values()->orderBy('order')->get() as $elementValue)
                                            <option @if($elementValue->id == old('element.'.$element->id.'')) selected="selected"
                                                    @elseif($building->buildingElements()->where('element_id', $element->id)->first() && $building->buildingElements()->where('element_id', $element->id)->first()->element_value_id == $elementValue->id) selected
                                                    @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                                        @endforeach
                                    </select>
                                @endcomponent

                                <div id="element_{{ $element->id }}-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{ $element->info }}
                                </div>

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
                                    <label for="user_interest_element_{{ $element->id }}"
                                           class="control-label small-text">{{\App\Helpers\Translation::translate('general.interested-in-improvement.title')}}
                                        <span>*</span></label>

    @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', 'element')->where('interested_in_id', $element->id),  'userInputColumn' => 'interest_id'])                                <select id="user_interest_element_{{ $element->id }}" class="form-control"
                                            name="user_interest[element][{{ $element->id }}]">
                                        @foreach($interests as $interest)
                                            <option @if($interest->id == old('user_interest.element.'. $element->id . ']')) selected
                                                    @elseif(Auth::user()->getInterestedType('element', $element->id) != null && Auth::user()->getInterestedType('element', $element->id)->interest_id == $interest->id) selected
                                                    @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                        @endforeach
                                    </select>@endcomponent

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
                                    <label for="{{$service->short}}" class="control-label">
                                        <i data-toggle="collapse" data-target="#service_{{ $service->id }}-info"
                                           class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                           aria-expanded="false"></i>
                                        {{ $service->name }}
                                    </label>
                                    {{-- This will check if the service has values, if so we need an selectbox and ifnot textbox --}}
                                    @if($service->values()->where('service_id', $service->id)->first() != null)

                                        <?php
                                        $selectedSV = old('service.' . $service->id, null);
                                        if (is_null($selectedSV)) {
                                            $buildServ = $building->buildingServices()->where('service_id', $service->id)->first();
                                            if ($buildServ instanceof \App\Models\BuildingService) {
                                                $selectedSV = $buildServ->service_value_id;
                                            }
                                        }
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
                                            <select id="{{$service->short}}" class="form-control"
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
                                                   value="@if(old('service.' . $service->id )){{old('service.' . $service->id)}} @elseif(isset($building->buildingServices()->where('service_id', $service->id)->first()->extra['value'])){{$building->buildingServices()->where('service_id', $service->id)->first()->extra['value']}} @endif"
                                                   name="service[{{ $service->id }}]">
                                        @endcomponent
                                    @endif

                                    <div id="service_{{ $service->id }}-info"
                                         class="collapse alert alert-info remove-collapse-space alert-top-space">
                                        {{ $service->info }}
                                    </div>

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
                                        <label for="user_interest_service_{{ $service->id }}"
                                               class="control-label small-text">{{\App\Helpers\Translation::translate('general.interested-in-improvement.title')}}</label>
                                        <span>*</span>

                @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', 'service')->where('interested_in_id', $service->id),  'userInputColumn' => 'interest_id'])                        <select id="user_interest_service_{{ $service->id }}" class="form-control"
                                                name="user_interest[service][{{ $service->id }}]">
                                            @foreach($interests as $interest)
                                                <option @if($interest->id == old('user_interest.service.' . $service->id )) selected
                                                        @elseif(Auth::user()->getInterestedType('service', $service->id) != null && Auth::user()->getInterestedType('service', $service->id)->interest_id == $interest->id) selected
                                                        @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                            @endforeach
                                        </select>@endcomponent

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
                                            <i data-toggle="collapse"
                                               data-target="#service_{{ $service->id }}-extra-info"
                                               class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                               aria-expanded="false"></i>
                                            @if(strpos($service->name, 'geventileerd'))
                                                {{\App\Helpers\Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.title')}}
                                            @elseif($service->short == 'total-sun-panels')
                                                {{\App\Helpers\Translation::translate('general-data.energy-saving-measures.sun-panel.if-yes.title')}}
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
                                            <input type="text" class="form-control"
                                                   name="{{ $service->id }}[extra][year]"
                                                   value="@if(old($service->id.'.extra.year')){{ old($service->id.'.extra.year') }}@elseif(isset($year)){{ $year }}@endif">
                                        @endcomponent

                                        <div id="service_{{ $service->id }}-extra-info"
                                             class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            {{\App\Helpers\Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.help')}}
                                        </div>

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

                    <h4 style="margin-left: -5px;">{{\App\Helpers\Translation::translate('general-data.data-about-usage.title.title')}}</h4>
            <div class="row">
                <div class="col-sm-6">

                    <div class="form-group add-space{{ $errors->has('resident_count') ? ' has-error' : '' }}">
                        <label for="resident_count" class=" control-label"><i data-toggle="collapse" data-target="#resident_count-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.total-citizens.title')}}
                                </label> <span>*</span>
                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'resident_count'])
                        <input type="text" id="resident_count" class="form-control" value="@if(old('resident_count') != ""){{old('resident_count')}}@elseif(isset($energyHabit)){{$energyHabit->resident_count}}@endif" name="resident_count" required>
                        @endcomponent

                    <div id="resident_count-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        {{\App\Helpers\Translation::translate('general-data.data-about-usage.total-citizens.help')}}
                    </div>

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
                            <i data-toggle="collapse" data-target="#cooked-on-gas-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.cooked-on-gas')
                        </label> <span>*</span>
                    <br>
                        <div class="input-group input-source-group">
                            <label class="radio-inline">
                                <input type="radio" name="cook_gas" @if(old('cook_gas') == 1) checked @elseif(isset($energyHabit) && $energyHabit->cook_gas == 1) checked @endif  value="1">@lang('woningdossier.cooperation.radiobutton.yes')
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="cook_gas" @if(old('cook_gas') == 2) checked @elseif(isset($energyHabit) && $energyHabit->cook_gas == 2 ) checked @endif value="2">@lang('woningdossier.cooperation.radiobutton.no')
                            </label>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    @foreach($userEnergyHabitsForMe as $userInputValue)
                                        <?php
                                            // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
                                            $value = $userInputValue->cook_gas;
                                            if ($value === 1) {
                                                $trans = __('woningdossier.cooperation.radiobutton.yes');
                                            } elseif($value === 2) {
                                                $trans = __('woningdossier.cooperation.radiobutton.no');
                                            }
                                        ?>

                                        <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$trans}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    <div id="cooked-on-gas-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        {{\App\Helpers\Translation::translate('general-data.data-about-usage.cooked-on-gas.help')}}
                    </div>

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
                                    <i data-toggle="collapse" data-target="#thermostat-high-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.thermostat-highest.title')}}
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'thermostat_high', 'needsFormat' => true])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.degrees.title')}}</span>
                                    <input type="text" id="thermostat_high" class="form-control"
                                           value="@if(!empty(old('thermostat_high'))){{ old('thermostat_high', 20) }}@elseif(isset($energyHabit)){{ \App\Helpers\NumberFormatter::format($energyHabit->thermostat_high, 1) }}@else{{ \App\Helpers\NumberFormatter::format(20, 1) }}@endif"
                                           name="thermostat_high">
                                @endcomponent

                                <div id="thermostat-high-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.thermostat-highest.help')}}
                                </div>

                                @if ($errors->has('thermostat_high'))
                                    <span class="help-block">
                        <strong>{{ $errors->first('thermostat_high') }}</strong>
                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="form-group add-space{{ $errors->has('thermostat_low') ? ' has-error' : '' }}">
                                <label for="thermostat_low" class=" control-label"><i data-toggle="collapse"
                                                                                      data-target="#thermostat-low-info"
                                                                                      class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                                                                      aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.thermostat-lowest.title')}}
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'thermostat_low', 'needsFormat' => true])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.degrees.title')}}</span>
                                    <input id="thermostat_low" type="text" class="form-control" name="thermostat_low"
                                           value="@if(!empty(old('thermostat_low'))){{ old('thermostat_low', 16) }}@elseif(isset($energyHabit)){{ \App\Helpers\NumberFormatter::format($energyHabit->thermostat_low, 1) }}@else{{ \App\Helpers\NumberFormatter::format(16, 1) }}@endif">
                                @endcomponent

                                <div id="thermostat-low-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.thermostat-lowest.help')}}
                                </div>

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
                                    <i data-toggle="collapse" data-target="#hours-high-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.max-hours-thermostat-highest.title')}}
                                </label>

                                <?php $hours =  range(1, 24)?>
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $hours, 'userInputValues' => $userEnergyHabitsForMe, 'userInputModel' => 'UserEnergyHabit', 'userInputColumn' => 'hours_high'])
                                    <span class="input-group-addon">Uren</span>
                                    <select id="hours_high" class="form-control" name="hours_high">
                                        @foreach($hours as $hour)
                                            <option @if($hour === old('hours_high')) selected @elseif(isset($energyHabit) && $energyHabit->hours_high == $hour) selected @elseif(!isset($energyHabit) && $hour == 12) selected @endif value="{{ $hour }}">{{ $hour }}</option>
                                        @endforeach
                                        <option @if($hour === old('hours_high')) selected @elseif(isset($energyHabit) && $energyHabit->hours_high == 0) selected @endif value="0">
                                            @lang('woningdossier.cooperation.radiobutton.not-important')
                                        </option>
                                    </select>
                                @endcomponent
                                <div id="hours-high-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{ \App\Helpers\Translation::translate('general-data.data-about-usage.max-hours-thermostat-highest.help') }}
                                </div>
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
                                    <i data-toggle="collapse" data-target="#heating-first-floor-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.situation-first-floor.title')}}
                                </label>

                                <?php

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

                                ?>

                                @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $buildingHeatings, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => $selectedHFFColumn])<select id="heating_first_floor" class="form-control" name="heating_first_floor">
                                    @foreach($buildingHeatings as $buildingHeating)
                                        <option @if(!is_null($selectedHFF) && $buildingHeating->id == $selectedHFF) selected="selected"
                                                @endif value="{{ $buildingHeating->id}}">{{ $buildingHeating->name }}</option>
                                    @endforeach
                                </select>@endcomponent

                                <div id="heating-first-floor-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.situation-first-floor.help')}}
                                </div>

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
                                    <i data-toggle="collapse"
                                       data-target="#heating-second-floor-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.situation-second-floor.title')}}
                                </label>

                                <?php

                                $selectedHSF = old('heating_second_floor', null);
            $selectedHSFColumn = 'heating_second_floor';                    if (is_null($selectedHSF)) {
                                    if (isset($energyHabit)) {
                                        $selectedHSFColumn = 'heating_second_floor';
			                    $selectedHSF = $energyHabit->heating_second_floor;
                                    }
                                }
                                if (is_null($selectedHSF)) {
                                    $selectedHeating = $buildingHeatings->where('is_default', '=', true)->first();
                                    if ($selectedHeating instanceof \App\Models\BuildingHeating) {$selectedHSFColumn = 'id';
                                        $selectedHSF = $selectedHeating->id;
                                    }
                                }

                                ?>

                                @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $buildingHeatings, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => $selectedHSFColumn])<select id="heating_second_floor" class="form-control" name="heating_second_floor">
                                    @foreach($buildingHeatings as $buildingHeating)
                                        <option @if(!is_null($selectedHSF) && $buildingHeating->id == $selectedHSF) selected="selected"
                                                @endif value="{{ $buildingHeating->id }}">{{ $buildingHeating->name }}</option>
                                    @endforeach
                                </select>@endcomponent

                                <div id="heating-second-floor-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.situation-second-floor.help')}}
                                </div>

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
                                    <i data-toggle="collapse"
                                       data-target="#comfortniveau-warm-tapwater-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.comfortniveau-warm-tapwater.title')}}
                                </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $comfortLevelsTapWater, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'water_comfort_id'])<select id="water_comfort" class="form-control" name="water_comfort" >
                            @foreach($comfortLevelsTapWater as $comfortLevelTapWater)
                                <option @if($comfortLevelTapWater->id == old('water_comfort')) selected @elseif(isset($energyHabit) && $energyHabit->water_comfort_id == $comfortLevelTapWater->id) selected @endif value="{{ $comfortLevelTapWater->id }}">{{ $comfortLevelTapWater->name }}</option>
                            @endforeach
                        </select>@endcomponent
                        <div id="comfortniveau-warm-tapwater-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('general-data.data-about-usage.comfortniveau-warm-tapwater.help')}}
                        </div>

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
                                    <i data-toggle="collapse"
                                       data-target="#amount-electricity-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.electricity-consumption-past-year.title')}} <span>*</span></label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'amount_electricity'])
                                    <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.kwh')</span>
                                    <input id="amount_electricity" required type="text"
                                           value="@if(old('amount_electricity') != ""){{ old('amount_electricity') }}@elseif(isset($energyHabit)){{ $energyHabit->amount_electricity }}@endif"
                                           class="form-control" name="amount_electricity" required>
                                @endcomponent

                                <div id="amount-electricity-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.electricity-consumption-past-year.help')}}
                                </div>

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
                                    <i data-toggle="collapse"
                                       data-target="#amount-gas-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.gas-usage-past-year.title')}}
                                    <span>*</span></label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'amount_gas'])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.cubic-meters.title')}}</span>
                                    <input id="amount_gas" type="text"
                                           value="@if(old('amount_gas') != ""){{ old('amount_gas') }}@elseif(isset($energyHabit)){{ $energyHabit->amount_gas }}@endif"
                                           class="form-control" name="amount_gas" required>
                                @endcomponent

                                <div id="amount-gas-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.gas-usage-past-year.help')}}
                                </div>


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
                                    <i data-toggle="collapse"
                                       data-target="#living-situation-extra-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                       aria-expanded="false"></i>{{\App\Helpers\Translation::translate('general-data.data-about-usage.additional-info.title')}}
                                </label>

                                <textarea id="additional-info" class="form-control"
                                          name="living_situation_extra">@if(old('living_situation_extra') != ""){{old('living_situation_extra')}}@elseif(isset($energyHabit)){{ $energyHabit->living_situation_extra }}@endif</textarea>

                                <div id="living-situation-extra-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.data-about-usage.additional-info.help')}}
                                </div>

                                @if ($errors->has('living_situation_extra'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('living_situation_extra') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                @if($coachEnergyHabitRemarks instanceof \App\Models\UserEnergyHabit && !empty($coachEnergyHabitRemarks->living_situation_extra))
                <div class="col-sm-12">
                        @component('cooperation.tool.components.alert')
                            {{ $coachEnergyHabitRemarks->living_situation_extra }}
                        @endcomponent
                </div>
                @endif
            </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('general-data.motivation.title.title')}} </h4>
                        </div>

                        {{-- Start at 1 so the translation will too. --}}
                        @for($i = 1; $i < 5; $i++)
                            <div class="col-sm-6">
                                <div class="form-group add-space{{ $errors->has('motivation.'.$i) ? ' has-error' : '' }}">
                                    <label for="motivation[{{ $i }}]" class=" control-label">
                                        <i data-toggle="collapse"
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
                                    <div id="motivation-{{ $i }}-info"
                                         class="collapse alert alert-info remove-collapse-space alert-top-space">
                                        {{\App\Helpers\Translation::translate('general-data.motivation.priority.help')}}
                                    </div>

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
                                <label for="motivation-extra" class=" control-label"><i data-toggle="collapse"
                                                                                        data-target="#motivation-extra-info"
                                                                                        class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                                                                        aria-expanded="false"></i>
                                    {{\App\Helpers\Translation::translate('general-data.motivation-extra.title')}}
                                </label>

                                <textarea id="motivation-extra" class="form-control"
                                          name="motivation_extra">@if(old('motivation_extra') != ""){{ old('motivation_extra') }}@elseif(isset($energyHabit)){{ $energyHabit->motivation_extra }}@endif</textarea>

                                <div id="motivation-extra-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('general-data.motivation-extra.help')}}
                                </div>

                                @if ($errors->has('motivation_extra'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('motivation_extra') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

        @if(\App\Models\BuildingService::hasCoachInputSource($userEnergyHabitsForMe) && Auth::user()->hasRole('resident'))
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space">
                        <?php
                            $coachInputSource = \App\Models\BuildingService::getCoachInput($userEnergyHabitsForMe);
                            $comment = $coachInputSource->living_situation_extra;
                        ?>
                        <label for="" class=" control-label"><i data-toggle="collapse" data-target="#comment" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('default.form.input.comment') ({{$coachInputSource->getInputSourceName()}})
                        </label>

                        <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                    </div>
                </div>
            </div>
        @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">{{\App\Helpers\Translation::translate('general.download.title')}}</div>
                            <div class="panel-body">
                                <ol>
                                    <?php $helpFile = "storage/hoomdossier-assets/Invul_hulp_Algemene_gegevens.pdf"; ?>
                                    <li><a download=""
                                           href="{{ asset($helpFile) }}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset($helpFile))))) }}</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

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

            // todo

            $('#building_type_id').change( function() {
                var buildingTypeId = $('#building_type_id').val();
                $.ajax({
                    type: "GET",
                    url: '{{route('cooperation.tool.general-data.example-building-type', ['cooperation' => $cooperation])}}',
                    data: {
                        building_type_id: buildingTypeId
                    },
                    success: function (data) {
                        // try to remove the old options wheter there is new information or not
                        $('#example_building_id option').remove();
                        // add a new empty option
                        $('#example_building_id').append($("<option></option>").attr('selected', true).text("---"));
                        if (data.length) {
                            // remove the old options so we don't get any duplicates or false options
                            $.each(data, function(index, exampleBuilding) {
                                var exampleBuildingName = exampleBuilding.real_name;
                                var exampleBuildingId = exampleBuilding.id;
                                $('#example_building_id').append($("<option></option>").attr("value", exampleBuildingId).text(exampleBuildingName));
                            });
                        } else {
                        }
                    }
                });
            });
            // if the user changes the build year trigger the example building cause different years have different content
            $('#build_year').change(function () {
                $('#example_building_id option').trigger('change');
            });

            var previous_eb = $("select#example_building_id").val();

            $("select#example_building_id").on('focus', function () {
                // Store the current value on focus and on change
                previous_eb = this.value;
            }).change(function() {
                // Do something with the previous value after the change
                var buildYear = $("input[name='build_year']").val();
                if (buildYear === ""){
                    @if(App::environment('local'))
                    console.log("Can't select example building: build year is empty");
                    @endif
                    $(this).val(previous_eb);
                }
                else {
                    if (this.value !== previous_eb ){
                        if (previous_eb === "" || confirm('@lang('woningdossier.cooperation.tool.general-data.example-building.apply-are-you-sure')')) {
                            @if(App::environment('local'))
                            console.log("Let's save it. EB id: " + this.value + " build year: " + buildYear);
                            @endif

                            $.ajax({
                                type: "POST",
                                url: '{{ route('cooperation.tool.apply-example-building', [ 'cooperation' => $cooperation ]) }}',
                                data: { example_building_id: this.value, build_year: buildYear },
                                success: function(data){
                                    location.reload();
                                }
                            });


                            // Make sure the previous value is updated
                            previous_eb = this.value;
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
                    $('#house-ventilation').parent().parent().next().next().show();
                } else {
                    $('#house-ventilation').parent().parent().next().next().hide();
                    $('#house-ventilation').parent().parent().next().next().find('input').val("");
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
                    extraField.find('input').val("");
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
        });
    </script>
@endpush