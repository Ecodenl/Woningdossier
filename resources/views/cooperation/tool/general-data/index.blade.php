@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.general-data.title'))

@push('css')
    <link rel="stylesheet" href="{{asset('css/datepicker/bootstrap-datepicker3.css')}}">
@endpush

@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.general-data.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div class="row">
            <div id="example-building" class="col-sm-12">
                <div class="form-group add-space{{ $errors->has('example_building_type') ? ' has-error' : '' }}">
                    <label for="example_building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#example-building-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.example-building-type')</label>
                    <select id="example_building_type_id" class="form-control" name="example_building_type" >
                        @foreach($exampleBuildingTypes as $exampleBuildingType)
                            <option @if($exampleBuildingType->id == old('example_building_type')) selected @endif value="{{ $exampleBuildingType->id }}">{{ $exampleBuildingType->name }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('example_building_type'))
                        <span class="help-block">
                    <strong>{{ $errors->first('example_building_type') }}</strong>
                </span>
                    @endif
                </div>

                <div class="col-sm-12">
                    <div class="form-group add-space">
                        <div id="example-building-type-info" class="collapse alert alert-info remove-collapse-space">
                            I would like to have some help full information right here!
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div id="building-type" class="col-md-12">
                <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.general-data.building-type.title')</h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('building_type_id') ? ' has-error' : '' }}">
                            <label for="building_type_id" class=" control-label"><i data-toggle="collapse" data-target="#building-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.what-type') </label>

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

                            <div id="building-type-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And I would like to have it too...
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
                            <label for="build_year" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.building-type.what-building-year')</label> <span>*</span>

                            <input id="build_year" type="text" class="form-control" name="build_year" value="@if(isset($building->buildingFeatures->build_year)){{ old('build_year', $building->buildingFeatures->build_year) }}@else {{ old('build_year') }}@endif" required autofocus>
                            <div id="what-building-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And I would like to have it too...
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
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('surface') ? ' has-error' : '' }}">
                            <label for="surface" class=" control-label"><i data-toggle="collapse" data-target="#user-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.what-user-surface')</label> <span>*</span>

                            <input id="surface" type="text" class="form-control" name="surface" value="@if(isset($building->buildingFeatures->surface)){{ old('surface', $building->buildingFeatures->surface) }}@else {{ old('surface') }}@endif" required autofocus>

                            <div id="user-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And I would like to have it too...
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
                            <label for="building_layers" class=" control-label"><i data-toggle="collapse" data-target="#roof-layers-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.how-much-building-layers')</label>

                            <input id="building_layers" type="text" class="form-control" name="building_layers" value="@if(isset($building->buildingFeatures->building_layers)){{ old('building_layers', $building->buildingFeatures->building_layers) }}@else {{ old('building_layers') }}@endif" req autofocus>

                            <div id="roof-layers-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And I would like to have it too...
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
                            <label for="roof_type_id" class=" control-label"><i data-toggle="collapse" data-target="#roof-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.type-roof')</label>

                            <select id="roof_type_id" class="form-control" name="roof_type_id" req>
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

                            <div id="roof-type-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And I would like to have it too...
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
                            <label for="energy_label_id" class=" control-label"><i data-toggle="collapse" data-target="#current-energy-label-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.current-energy-label')</label>

                            <select id="energy_label_id" class="form-control" name="energy_label_id" req>
                                @foreach($energyLabels as $energyLabel)
                                    <option
                                            @if(old('energy_label_id') && $buildingType->id == old('energy_label_id'))
                                            selected="selected"
                                            @elseif(isset($building->buildingFeatures->energyLabel) && $building->buildingFeatures->energyLabel->id == $energyLabel->id)
                                            selected="selected"
                                            @endif
                                            value="{{ $energyLabel->id }}">{{ $energyLabel->name }}</option>
                                @endforeach
                            </select>
                            <div id="current-energy-label-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And I would like to have it too...
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
                        <div class="form-group add-space{{ $errors->has('monument') ? ' has-error' : '' }}">
                            <label for="monument" class=" control-label"><i data-toggle="collapse" data-target="#is-monument-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.is-monument')</label>

                            <label class="radio-inline">
                                <input type="radio" name="monument" value="1" @if(isset($building->buildingFeatures->monument) && $building->buildingFeatures->monument == 1) checked @elseif(old('monument') == 1) checked @endif>@lang('woningdossier.cooperation.radiobutton.yes')
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="monument" value="2" @if(isset($building->buildingFeatures->monument) && $building->buildingFeatures->monument == 2) checked @elseif(old('monument') == 2) checked @endif>@lang('woningdossier.cooperation.radiobutton.no')
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="monument" value="0" @if(isset($building->buildingFeatures->monument) && $building->buildingFeatures->monument == "0") checked @elseif(old('monument') == "0") checked @endif>@lang('woningdossier.cooperation.radiobutton.unknown')
                            </label>

                            <div id="is-monument-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And I would like to have it too...
                            </div>

                            @if ($errors->has('monument'))
                                <span class="help-block">
                                <strong>{{ $errors->first('monument') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div id="energy-saving-measures">
            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.title')</h4>

            @foreach($elements as $i => $element)
                @if ($i % 2 == 0)
                    <div class="row">
                @endif

                        <div class="col-sm-4">
                            <div class="form-group add-space{{ $errors->has('element.'.$element->id) ? ' has-error' : '' }}">
                                <label for="element_{{ $element->id }}" class="control-label">
                                    <i data-toggle="collapse" data-target="#element_{{ $element->id }}-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                    {{ $element->name }}
                                </label>
                                <select id="element_{{ $element->id }}" class="form-control" name="element[{{ $element->id }}]">
                                    @foreach($element->values()->orderBy('order')->get() as $elementValue)
                                        <option @if($elementValue->id == old('element['. $element->id.']')) selected="selected" @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                                    @endforeach
                                </select>

                                <div id="element_{{ $element->id }}-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{ $element->info }}
                                </div>

                                @if ($errors->has('element.' . $element->id))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('element.' . $element->id) }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group add-space{{ $errors->has('user_interest.element.' . $element->id) ? ' has-error' : '' }}">
                                <label for="user_interest_element_{{ $element->id }}" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                                <select id="user_interest_element_{{ $element->id }}" class="form-control" name="user_interest[element][{{ $element->id }}]" >
                                    @foreach($interests as $interest)
                                        <option @if($interest->id == old('user_interest[element][' . $element->id . ']')) selected @elseif(Auth::user()->getInterestedType('element', $element->id) != null && Auth::user()->getInterestedType('element', $element->id)->interest_id == $interest->id) selected @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('user_interest.element.' . $element->id))
                                    <span class="help-block">
                                <strong>{{ $errors->first('user_interest.element.' . $element->id) }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>

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
            @if ($i % 2 == 0 || strpos($service->name, 'geventileerd') || strpos($service->name, 'zonnepanelen'))
                <div class="row" id="service_row_{{$service->id}}">
            @endif

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('service.'.$service->id) ? ' has-error' : '' }}">
                        <label for="{{$service->short}}" class="control-label">
                            <i data-toggle="collapse" data-target="#service_{{ $service->id }}-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                            {{ $service->name }}
                        </label>
                        {{-- This will check if the service has values, if so we need an selectbox and ifnot textbox --}}
                        @if($service->values()->where('service_id', $service->id)->first() != null)
                            <select id="{{$service->short}}" class="form-control" name="service[{{ $service->id }}]">
                                @foreach($service->values()->orderBy('order')->get() as $serviceValue)
                                    <option @if(old('service.'.$service->id) == $serviceValue->id) selected="selected" @elseif($building->buildingServices()->where('service_id', $service->id)->first() != null && $building->buildingServices()->where('service_id', $service->id)->first()->service_value_id == $serviceValue->id) selected @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" id="{{$service->short}}" class="form-control" value="@if(old('service.'.$service->id)) {{old('service.'.$service->id)}} @elseif(isset($building->buildingServices()->where('service_id', $service->id)->first()->extra['value'])){{$building->buildingServices()->where('service_id', $service->id)->first()->extra['value']}} @endif" name="service[{{ $service->id }}]">
                        @endif

                        <div id="service_{{ $service->id }}-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{ $service->info }}
                        </div>

                        @if ($errors->has('service.' . $service->id))
                            <span class="help-block">
                                <strong>{{ $errors->first('service.' . $service->id) }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('user_interest.service.' . $service->id) ? ' has-error' : '' }}">
                        <label for="user_interest_service_{{ $service->id }}" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="user_interest_service_{{ $service->id }}" class="form-control" name="user_interest[service][{{ $service->id }}]" >
                            @foreach($interests as $interest)
                                <option @if($interest->id == old('user_interest[service][' . $service->id . ']')) selected @elseif(Auth::user()->getInterestedType('service', $service->id) != null && Auth::user()->getInterestedType('service', $service->id)->interest_id == $interest->id) selected @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('user_interest.service.' . $service->id))
                            <span class="help-block">
                            <strong>{{ $errors->first('user_interest.service.' . $service->id) }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                @if(strpos($service->name, 'geventileerd') || strpos($service->name, 'zonnepanelen'))
                    <div class="col-sm-6 {{ $errors->has(''.$service->id.'.extra') ? ' show' : '' }}">

                        <div id="{{$service->id.'-extra'}}" class="form-group add-space{{ $errors->has(''.$service->id.'.extra') ? ' has-error' : '' }}">
                            <label for="service_{{ $service->id }}" class="control-label">
                                <i data-toggle="collapse" data-target="#service_{{ $service->id }}-extra-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                @if(strpos($service->name, 'geventileerd'))
                                    @lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.house-ventilation.if-mechanic')
                                @elseif(strpos($service->name, 'zonnepanelen'))
                                    @lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.sun-panel.if-yes')
                                @endif

                            </label>
                            <?php
                                if(isset($building->buildingServices()->where('service_id', $service->id)->first()->extra['date'])) {
                                    $date = $building->buildingServices()->where('service_id', $service->id)->first()->extra['date'];
                                    $date = \Carbon\Carbon::parse($date)->format('d-m-Y');
                                }
                            ?>

                            <div class="input-group date">
                                <input type="text" class="form-control" name="{{$service->id.'[extra]'}}" value="@if(old($service->id.'.extra')) {{old($service->id.'.extra')}} @elseif(isset($date)){{$date}} @endif"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                            <div id="service_{{ $service->id }}-extra-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And I would like to have it too...
                            </div>

                            @if ($errors->has($service->id.'.extra'))
                                <span class="help-block">
                                    <strong>{{ $errors->first($service->id.'.extra') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif



                @if ($i % 2 == 1 || strpos($service->name, 'geventileerd') || strpos($service->name, 'zonnepanelen'))
                    </div>
                @endif
        @endforeach

                {{-- note that because of ->count, odd counts % 2 result in 1, whereas $i starts with 0 and therefore has the inverted result --}}
                @if($services->count() % 2 == 1)
                    {{-- last (row) div was not closed. Close it --}}
                    </form>
                @endif
            {{-- Close the measure div --}}
            </div>



        <div id="data-about-usage">

            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.general-data.data-about-usage.title')</h4>
            <div class="row">
            <div class="col-sm-6">

                <div class="form-group add-space{{ $errors->has('resident_count') ? ' has-error' : '' }}">
                    <label for="resident_count" class=" control-label"><i data-toggle="collapse" data-target="#resident_count-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.total-citizens')</label> <span>*</span>

                    <input type="text" id="resident_count" class="form-control" value="@if(old('resident_count') != "") {{old('resident_count')}} @elseif(isset($energyHabit)) {{$energyHabit->resident_count}} @endif" name="resident_count" required>

                    <div id="resident_count-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        And I would like to have it too...
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
                    <label for="cook_gas" class=" control-label"><i data-toggle="collapse" data-target="#cooked-on-gas-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.cooked-on-gas')</label>
                    <label class="radio-inline">
                        <input type="radio" name="cook_gas" @if(old('cook_gas') == 1) checked @elseif(isset($energyHabit) && $energyHabit->cook_gas == 1) checked @endif  value="1">@lang('woningdossier.cooperation.radiobutton.yes')
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="cook_gas" @if(old('cook_gas') == 2) checked @elseif(isset($energyHabit) && $energyHabit->cook_gas == 2 ) checked @endif value="2">@lang('woningdossier.cooperation.radiobutton.no')
                    </label>

                    <div id="cooked-on-gas-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        And I would like to have it too...
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
                    <label for="thermostat_high" class=" control-label"><i data-toggle="collapse" data-target="#thermostat-high-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.thermostat-highest')</label>

                    <input type="text" id="thermostat_high" class="form-control" value="@if(old('thermostat_high') != "") {{old('thermostat_high')}} @elseif(isset($energyHabit)) {{$energyHabit->thermostat_high}} @endif" name="thermostat_high">

                    <div id="thermostat-high-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        And I would like to have it too...
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
                    <label for="thermostat_low" class=" control-label"><i data-toggle="collapse" data-target="#thermostat-low-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.thermostat-lowest')</label>

                    <input id="thermostat_low" type="text" class="form-control" name="thermostat_low" value="@if(old('thermostat_low') != "") {{old('thermostat_low')}} @elseif(isset($energyHabit)) {{$energyHabit->thermostat_low}} @endif" placeholder="{{old('thermostat_low')}}">

                    <div id="thermostat-low-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        And I would like to have it too...
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
                        <label for="hours_high" class=" control-label"><i data-toggle="collapse" data-target="#hours-hight-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.max-hours-thermostat-highest')</label>

                        <select id="hours_high" class="form-control" name="hours_high">
                            @for($hour = 0; $hour < 25; $hour++)
                                <option @if($hour == old('hours_high')) selected @elseif(isset($energyHabit) && $energyHabit->hours_high == $hour) selected @endif value="{{ $hour }}">{{ $hour }}</option>
                            @endfor
                                <option @if($hour == old('hours_high')) selected @elseif(isset($energyHabit) && $energyHabit->hours_high == 0) selected @endif value="0">@lang('woningdossier.cooperation.radiobutton.not-important')</option>
                        </select>

                        <div id="hours-high-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And I would like to have it too...
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
                        <label for="heating_first_floor" class=" control-label"><i data-toggle="collapse" data-target="#heating-first-floor-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.situation-first-floor')</label>

                        <select id="heating_first_floor" class="form-control" name="heating_first_floor" >
                            @foreach($buildingHeatings as $buildingHeating)
                                <option @if($buildingHeating->id == old('heating_first_floor')) selected @elseif(isset($energyHabit) && $energyHabit->heating_first_floor == $buildingHeating->id) selected @endif value="{{ $buildingHeating->id}}">{{ $buildingHeating->name }}</option>
                            @endforeach

                        </select>

                        <div id="heating-first-floor-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And I would like to have it too...
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
                        <label for="heating_second_floor" class=" control-label"><i data-toggle="collapse" data-target="#heating-second-floor-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.situation-second-floor')</label>

                        <select id="heating_second_floor" class="form-control" name="heating_second_floor" >
                            @foreach($buildingHeatings as $buildingHeating)
                                <option @if($buildingHeating->id == old('heating_second_floor')) selected @elseif(isset($energyHabit) && $energyHabit->heating_second_floor == $buildingHeating->id) selected @endif value="{{ $buildingHeating->id }}">{{ $buildingHeating->name }}</option>
                            @endforeach
                        </select>

                        <div id="heating-second-floor-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And I would like to have it too...
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
                        <label for="water_comfort" class=" control-label"><i data-toggle="collapse" data-target="#comfortniveau-warm-tapwater-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.comfortniveau-warm-tapwater')</label>

                        <select id="water_comfort" class="form-control" name="water_comfort" >
                            @foreach($comfortLevelsTapWater as $comfortLevelTapWater)
                                <option @if($comfortLevelTapWater->id == old('water_comfort')) selected @elseif(isset($energyHabit) && $energyHabit->water_comfort_id == $comfortLevelTapWater->id) selected @endif value="{{$comfortLevelTapWater->id}}">{{$comfortLevelTapWater->name}}</option>
                            @endforeach
                        </select>
                        <div id="comfortniveau-warm-tapwater-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And I would like to have it too...
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
                        <label for="amount_electricity" class=" control-label"><i data-toggle="collapse" data-target="#amount-electricity-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.electricity-consumption-past-year')</label>


                        <input id="amount_electricity" type="text" value="@if(old('amount_electricity') != ""){{ old('amount_electricity') }}@elseif(isset($energyHabit)){{ $energyHabit->amount_electricity }}@endif" class="form-control" name="amount_electricity">

                        <div id="amount-electricity-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And I would like to have it too...
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
                        <label for="amount_gas" class=" control-label"><i data-toggle="collapse" data-target="#amount-gas-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.gas-usage-past-year') <span>*</span></label>

                        <input id="amount_gas" type="text" value="@if(old('amount_gas') != ""){{ old('amount_gas') }}@elseif(isset($energyHabit)){{ $energyHabit->amount_gas }}@endif" class="form-control" name="amount_gas" required>
                        <div id="amount-gas-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And I would like to have it too...
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
                        <label for="additional-info" class=" control-label"><i data-toggle="collapse" data-target="#living-situation-extra-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.additional-info')</label>

                        <textarea id="additional-info" class="form-control" name="living_situation_extra">@if(old('living_situation_extra') != ""){{old('living_situation_extra')}}@elseif(isset($energyHabit)){{$energyHabit->living_situation_extra}}@endif</textarea>

                        <div id="living-situation-extra-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And I would like to have it too...
                        </div>

                        @if ($errors->has('living_situation_extra'))
                            <span class="help-block">
                                <strong>{{ $errors->first('living_situation_extra') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('motivation_extra') ? ' has-error' : '' }}">
                        <label for="motivation-extra" class=" control-label"><i data-toggle="collapse" data-target="#motivation-extra-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.motivation-extra')</label>

                        <textarea id="motivation-extra" class="form-control" name="motivation_extra">@if(old('motivation_extra') != ""){{old('motivation_extra')}}@elseif(isset($energyHabit)){{$energyHabit->motivation_extra}}@endif</textarea>

                        <div id="motivation-extra-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And I would like to have it too...
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
    <script src="{{asset('js/datepicker/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('js/datepicker/bootstrap-datepicker.nl.min.js')}}"></script>
    <script>

        $(document).ready(function () {

            // Load the datepicker
            $('.input-group.date').datepicker({
                language: "nl"
            });

            // Check if the house ventialtion is mechanic
            $(document).change('#house-ventilation', function () {

                // Housse ventilation
                var houseVentilation = $('#house-ventilation option:selected').text()

                // text wont change, id's will
                if (houseVentilation == "Mechanisch" || houseVentilation == "Decentraal mechanisch") {
                    $('#house-ventilation').parent().parent().next().next().show();
                } else {
                    $('#house-ventilation').parent().parent().next().next().hide();
                    $('#house-ventilation').parent().parent().next().next().find('input').val("")
                }
            });

            // check if a user is interested in a sun panel
            $(document).change('#total-sun-panels', function() {
                var totalSunPanels = $('#total-sun-panels').val();
                // var extraFieldSunPanel =  $('#total-sun-panels').parent().parent().next().next();

                if(totalSunPanels > 0) {
                    $('#total-sun-panels').parent().parent().next().next().show();
                } else {
                    $('#total-sun-panels').parent().parent().next().next().hide();
                    // Clear the value off the date
                    $('#total-sun-panels').parent().parent().next().next().find('input').val("")
                }

            });

            $('#house-ventilation, #total-sun-panels').trigger('change')
        });
    </script>
@endpush