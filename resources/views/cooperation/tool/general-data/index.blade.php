@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.general-data.title'))

@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.general-data.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div class="row">
            <div id="example-building" class="col-sm-12">
                <div class="form-group add-space{{ $errors->has('example_building_type') ? ' has-error' : '' }}">
                    <label for="example_building_type" class=" control-label"><i data-toggle="collapse" data-target="#example-building-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.example-building-type')</label>
                    <select id="example_building_type" class="form-control" name="example_building_type" >
                        @foreach($exampleBuildingTypes as $exampleBuildingType)
                            <option @if($exampleBuildingType->id == old('example_building_type')) selected @endif value="{{$exampleBuildingType->id}}">@lang($exampleBuildingType->translation_key)</option>
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
                            I would like to have some help full information right here !
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{--
            <div id="main-data" class="col-md-6">
                <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.general-data.name-address-data.title')</h4>
                    <div class="form-group add-space{{ $errors->has('name_resident') ? ' has-error' : '' }}">
                        <label for="name_resident" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.name-address-data.name-resident')</label>

                        <input id="name_resident" type="text" class="form-control" name="name_resident" value="{{old('name_resident')}}" req autofocus>

                        @if ($errors->has('name_resident'))
                            <span class="help-block">
                        <strong>{{ $errors->first('name_resident') }}</strong>
                    </span>
                        @endif
                    </div>

                    <div class="form-group add-space{{ $errors->has('street') ? ' has-error' : '' }}">
                        <label for="street" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.name-address-data.street')</label>

                        <input id="street" type="text" class="form-control" name="street" value="{{old('street')}}" req autofocus>

                        @if ($errors->has('street'))
                            <span class="help-block">
                        <strong>{{ $errors->first('street') }}</strong>
                    </span>
                        @endif
                    </div>

                    <div class="form-group add-space{{ $errors->has('house_number') ? ' has-error' : '' }}">
                        <label for="house-number" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.name-address-data.house-number')</label>

                        <input id="house_number" type="text" class="form-control" name="house_number" value="{{old('house_number')}}" req autofocus>

                        @if ($errors->has('house_number'))
                            <span class="help-block">
                        <strong>{{ $errors->first('house_number') }}</strong>
                    </span>
                        @endif
                    </div>



                <div class="form-group add-space{{ $errors->has('zip_code') ? ' has-error' : '' }}">
                    <label for="zip_code" class="control-label">@lang('woningdossier.cooperation.tool.general-data.name-address-data.zip-code')</label>

                    <input id="zip_code" type="text" class="form-control" name="zip_code" value="{{old('zip_code')}}" req autofocus>

                    @if ($errors->has('zip_code'))
                        <span class="help-block">
                        <strong>{{ $errors->first('zip_code') }}</strong>
                    </span>
                    @endif
                </div>



                <div class="form-group add-space{{ $errors->has('residence') ? ' has-error' : '' }}">
                    <label for="residence" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.name-address-data.residence')</label>

                    <input id="residence" type="text" class="form-control" name="residence" value="{{old('residence')}}" req autofocus>

                    @if ($errors->has('residence'))
                        <span class="help-block">
                        <strong>{{ $errors->first('residence') }}</strong>
                    </span>
                    @endif
                </div>



                <div class="form-group add-space{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.name-address-data.email')</label>

                    <input id="email" type="text" class="form-control" name="email" value="{{old('email')}}" req autofocus>

                    @if ($errors->has('email'))
                        <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>



                <div class="form-group add-space{{ $errors->has('phone_number') ? ' has-error' : '' }}">
                    <label for="phone_number" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.name-address-data.phone-number')</label>

                    <input id="phone_number" type="text" class="form-control" name="phone_number" value="{{old('phone_number')}}" req autofocus>

                    @if ($errors->has('phone_number'))
                        <span class="help-block">
                        <strong>{{ $errors->first('phone_number') }}</strong>
                    </span>
                    @endif
                </div>

            </div>
            --}}
            <div id="building-type" class="col-md-12">
                <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.general-data.building-type.title')</h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('building_type') ? ' has-error' : '' }}">
                            <label for="building_type" class=" control-label"><i data-toggle="collapse" data-target="#building-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.what-type') </label>

                            <select id="building_type" class="form-control" name="building_type">
                                @foreach($buildingTypes as $buildingType)
                                    <option @if($buildingType->id == old('building_type')) selected @endif value="{{$buildingType->id}}">{{$buildingType->name}}</option>
                                @endforeach
                            </select>

                            <div id="building-type-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>
                            @if ($errors->has('building_type'))
                                <span class="help-block">
                                <strong>{{ $errors->first('building_type') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('what_building_year') ? ' has-error' : '' }}">
                            <label for="what_building_year" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.building-type.what-building-year')</label> <span>*</span>

                            <input id="what_building_year" type="text" class="form-control" name="what_building_year" value="{{old('what_building_year')}}" required autofocus>
                            <div id="what-building-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>
                            @if ($errors->has('what_building_year'))
                                <span class="help-block">
                                <strong>{{ $errors->first('what_building_year') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('user_surface') ? ' has-error' : '' }}">
                            <label for="user_surface" class=" control-label"><i data-toggle="collapse" data-target="#user-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.what-user-surface')</label> <span>*</span>

                            <input id="user_surface" type="text" class="form-control" name="user_surface" value="{{old('user_surface')}}" required autofocus>

                            <div id="user-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('user_surface'))
                                <span class="help-block">
                                <strong>{{ $errors->first('user_surface') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('roof_layers') ? ' has-error' : '' }}">
                            <label for="roof_layers" class=" control-label"><i data-toggle="collapse" data-target="#roof-layers-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.how-much-building-layers')</label>

                            <input id="roof_layers" type="text" class="form-control" name="roof_layers" value="{{old('roof_layers')}}" req autofocus>

                            <div id="roof-layers-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('roof_layers'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('roof_layers') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('roof_type') ? ' has-error' : '' }}">
                            <label for="roof_type" class=" control-label"><i data-toggle="collapse" data-target="#roof-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.type-roof')</label>

                            <select id="roof_type" class="form-control" name="roof_type" req>
                                @foreach($roofTypes as $roofType)
                                    <option @if($roofType->id == old('roof_type')) selected @endif value="{{$roofType->id}}">{{ $roofType->name }}</option>
                                @endforeach
                            </select>

                            <div id="roof-type-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('roof_type'))
                                <span class="help-block">
                                <strong>{{ $errors->first('roof_type') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group add-space{{ $errors->has('current_energy_label') ? ' has-error' : '' }}">
                            <label for="current_energy_label" class=" control-label"><i data-toggle="collapse" data-target="#current-energy-label-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.current-energy-label')</label>

                            <select id="current_energy_label" class="form-control" name="current_energy_label" req>
                                @foreach($energyLabels as $energyLabel)
                                    <option @if($energyLabel->id == old('current_energy_label')) selected @endif value="{{$energyLabel->id}}">{{$energyLabel->name}}</option>
                                @endforeach
                            </select>
                            <div id="current-energy-label-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('current_energy_label'))
                                <span class="help-block">
                                <strong>{{ $errors->first('current_energy_label') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group add-space{{ $errors->has('is_monument') ? ' has-error' : '' }}">
                            <label for="is_monument" class=" control-label"><i data-toggle="collapse" data-target="#is-monument-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.building-type.is-monument')</label>

                            <label class="radio-inline">
                                <input type="radio" name="is_monument" value="1">@lang('woningdossier.cooperation.radiobutton.yes')
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_monument" value="2">@lang('woningdossier.cooperation.radiobutton.no')
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_monument" value="0">@lang('woningdossier.cooperation.radiobutton.unknown')
                            </label>

                            <div id="is-monument-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('is_monument'))
                                <span class="help-block">
                                <strong>{{ $errors->first('is_monument') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div id="energy-saving-measures">
            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.title')</h4>
            <div class="row">

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('windows_in_living_space') ? ' has-error' : '' }}">
                        <label for="windows_in_living_space" class=" control-label"><i data-toggle="collapse" data-target="#windows-in-living-space-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.window-in-living-space')</label> <span>*</span>

                        <select id="windows_in_living_space" class="form-control" name="windows_in_living_space" >
                            @foreach($insulations as $insulation)
                                <option @if($insulation->id == old('windows_in_living_space')) selected @endif value="{{$insulation->id}}">{{$insulation->name}}</option>
                            @endforeach
                        </select>

                        <div id="windows-in-living-space-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('windows_in_living_space'))
                            <span class="help-block">
                                <strong>{{ $errors->first('windows_in_living_space') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[windows_in_living_space]') ? ' has-error' : '' }}">
                        <label for="windows_in_living_space" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="windows_in_living_space" class="form-control" name="interested[windows_in_living_space]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[windows_in_living_space]')) selected @endif value="{{$interested->id}}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[windows_in_living_space]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[windows_in_living_space]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('windows_in_sleeping_spaces') ? ' has-error' : '' }}">
                        <label for="windows_in_sleeping_spaces" class=" control-label"><i data-toggle="collapse" data-target="#windows-is-sleeping-spaces-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.window-in-sleeping-spaces')</label> <span>*</span>

                        <select id="windows_in_sleeping_spaces" class="form-control" name="windows_in_sleeping_spaces" >
                            @foreach($insulations as $insulation)
                                <option @if($insulation->id == old('windows_in_sleeping_spaces')) selected @endif value="{{$insulation->id}}">{{$insulation->name}}</option>
                            @endforeach
                        </select>

                        <div id="windows-in-sleeping-spaces-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('windows_in_sleeping_spaces'))
                            <span class="help-block">
                        <strong>{{ $errors->first('windows_in_sleeping_spaces') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[windows_in_sleeping_spaces]') ? ' has-error' : '' }}">
                        <label for="windows_in_sleeping_spaces" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="windows_in_sleeping_spaces" class="form-control" name="interested[windows_in_sleeping_spaces]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[windows_in_sleeping_spaces]')) selected @endif value="{{$interested->id}}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[windows_in_sleeping_spaces]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[windows_in_sleeping_spaces]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-4">

                    <div class="form-group add-space{{ $errors->has('facade_insulation') ? ' has-error' : '' }}">
                        <label for="facade_insulation" class=" control-label"><i data-toggle="collapse" data-target="#facade-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.facade-insulation')</label> <span>*</span>

                        <select id="facade_insulation" class="form-control" name="facade_insulation" >
                            @foreach($qualities as $quality)
                                <option @if($quality->id == old('facade_insulation')) selected @endif value="{{$quality->id}}">{{$quality->name}}</option>
                            @endforeach
                        </select>

                        <div id="facade-insulation-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('facade_insulation'))
                            <span class="help-block">
                        <strong>{{ $errors->first('facade_insulation') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[facade_insulation]') ? ' has-error' : '' }}">
                        <label for="facade_insulation" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="facade_insulation" class="form-control" name="interested[facade_insulation]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[facade_insulation]')) selected @endif value="{{$interested->id}}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[facade_insulation]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[facade_insulation]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('floor_insulation') ? ' has-error' : '' }}">
                        <label for="floor_insulation" class=" control-label"><i data-toggle="collapse" data-target="#floor-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.floor-insulation')</label> <span>*</span>

                        <select id="floor_insulation" class="form-control" name="floor_insulation" >
                            @foreach($qualities as $quality)
                                <option @if($quality->id == old('floor_insulation')) selected @endif value="{{$quality->id}}">{{$quality->name}}</option>
                            @endforeach
                        </select>

                        <div id="floor-insulation-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('floor_insulation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('floor_insulation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[floor_insulation]') ? ' has-error' : '' }}">
                        <label for="floor_insulation" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="floor_insulation" class="form-control" name="interested[floor_insulation]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[floor_insulation]')) selected @endif value="{{ $interested->id }}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[floor_insulation]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[floor_insulation]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">

                    <div class="form-group add-space{{ $errors->has('roof_insulation') ? ' has-error' : '' }}">
                        <label for="roof_insulation" class=" control-label"><i data-toggle="collapse" data-target="#roof-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.roof-insulation')</label> <span>*</span>

                        <select id="roof_insulation" class="form-control" name="roof_insulation" >
                            @foreach($qualities as $quality)
                                <option @if($quality->id == old('roof_insulation')) selected @endif value="{{ $quality->id}}">{{$quality->name}}</option>
                            @endforeach
                        </select>

                        <div id="roof-insulation-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('roof_insulation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('roof_insulation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('interested[roof_insulation]') ? ' has-error' : '' }}">
                        <label for="roof_insulation" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="roof_insulation" class="form-control" name="interested[roof_insulation]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[roof_insulation]')) selected @endif value="{{ $interested->id }}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[roof_insulation]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[roof_insulation]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('hr_cv_boiler') ? ' has-error' : '' }}">
                        <label for="hr_cv_boiler" class=" control-label"><i data-toggle="collapse" data-target="#hr-cv-boiler-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.hr-cv-boiler')</label> <span>*</span>

                        <select id="hr_cv_boiler" class="form-control" name="hr_cv_boiler" >
                            @foreach($centralHeatingAges as $centralHeatingAge)
                                <option @if($centralHeatingAge->id == old('hr_cv_boiler')) selected @endif value="{{ $centralHeatingAge->id}}">{{$centralHeatingAge->name}}</option>
                            @endforeach
                        </select>

                        <div id="hr-cv-boiler-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>
                        @if ($errors->has('hr_cv_boiler'))
                            <span class="help-block">
                        <strong>{{ $errors->first('hr_cv_boiler') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[hr_cv_boiler]') ? ' has-error' : '' }}">
                        <label for="hr_cv_boiler" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="hr_cv_boiler" class="form-control" name="interested[hr_cv_boiler]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[hr_cv_boiler]')) selected @endif value="{{$interested->id}}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[hr_cv_boiler]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[hr_cv_boiler]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('hybrid_heatpump') ? ' has-error' : '' }}">
                        <label for="hybrid_heatpump" class=" control-label"><i data-toggle="collapse" data-target="#hybrid-heatpump-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.hybrid-heatpump')</label> <span>*</span>

                        <select id="hybrid_heatpump" class="form-control" name="hybrid_heatpump" >
                            @foreach($heatPumps->take(2)  as $heatPump)
                                <option @if($heatPump->id ==  old('hybrid_heatpump')) selected @endif value="{{$heatPump->id}}">@lang($heatPump->name)</option>
                            @endforeach
                        </select>

                        <div id="hybrid-heatpump-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('hybrid_heatpump'))
                            <span class="help-block">
                        <strong>{{ $errors->first('hybrid_heatpump') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[hybrid_heatpump]') ? ' has-error' : '' }}">
                        <label for="hybrid_heatpump" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="hybrid_heatpump" class="form-control" name="interested[hybrid_heatpump]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[hybrid_heatpump]')) selected @endif value="{{$interested->id}}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[hybrid_heatpump]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[hybrid_heatpump]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('sun_panel') ? ' has-error' : '' }}">
                        <label for="sun_panel" class=" control-label"><i data-toggle="collapse" data-target="#sun-panel-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.sun-panel.title')</label>

                        <input type="text" id="sun_panel" class="form-control" name="sun_panel">

                        <div id="sun-panel-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('sun_panel'))
                            <span class="help-block">
                        <strong>{{ $errors->first('sun_panel') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[sun_panel]') ? ' has-error' : '' }}">
                        <label for="sun_panel" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label>

                        <select id="sun_panel" class="form-control" name="interested[sun_panel]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[sun_panel]')) selected @endif value="{{$interested->id}}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[sun_panel]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[sun_panel]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('sun_panel_placed_date') ? ' has-error' : '' }}">
                        <label for="sun_panel_placed_date" class=" control-label"><i data-toggle="collapse" data-target="#sun-panel-placed-date-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.sun-panel.if-mechanic')</label>

                        <input type="date" name="sun_panel_placed_date" id="sun_panel_placed_date" class="form-control" value="{{old('sun_panel_placed_date')}}">

                        <div id="sun-panel-placed-date-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('sun_panel_placed_date'))
                            <span class="help-block">
                        <strong>{{ $errors->first('sun_panel_placed_date') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('monovalent_heatpump') ? ' has-error' : '' }}">
                        <label for="monovalent_heatpump" class=" control-label"><i data-toggle="collapse" data-target="#monovalent-heatpump-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.monovalent-heatpump')</label> <span>*</span>

                        <select id="monovalent_heatpump" class="form-control" name="monovalent_heatpump" >
                            @foreach($heatPumps->forget(1)  as $heatPump)
                                <option @if($heatPump->id == old('monovalent_heatpump')) selected @endif value="{{  $heatPump->id }}">@lang($heatPump->name)</option>
                            @endforeach
                        </select>

                        <div id="monovalent-heatpump-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('monovalent_heatpump'))
                            <span class="help-block">
                        <strong>{{ $errors->first('monovalent_heatpump') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[monovalent_heatpump]') ? ' has-error' : '' }}">
                        <label for="monovalent_heatpump" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="monovalent_heatpump" class="form-control" name="interested[monovalent_heatpump]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[monovalent_heatpump]')) selected @endif value="{{  $interested->id }}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[monovalent_heatpump]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[monovalent_heatpump]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group add-space{{ $errors->has('sun_boiler') ? ' has-error' : '' }}">
                        <label for="sun_boiler" class=" control-label"><i data-toggle="collapse" data-target="#sun-boiler-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.sun-boiler')</label> <span>*</span>

                        <select id="sun_boiler" class="form-control" name="sun_boiler" >
                            @foreach($solarWaterHeaters as $solarWaterHeater)
                                <option @if($solarWaterHeater->id == old('sun_boiler')) selected @endif value="{{$solarWaterHeater->id}}">{{$solarWaterHeater->name}}</option>
                            @endforeach
                        </select>
                        <div id="sun-boiler-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>
                        @if ($errors->has('sun_boiler'))
                            <span class="help-block">
                        <strong>{{ $errors->first('sun_boiler') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[sun_boiler]') ? ' has-error' : '' }}">
                        <label for="sun_boiler" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label> <span>*</span>

                        <select id="sun_boiler" class="form-control" name="interested[sun_boiler]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[sun_boiler]')) selected @endif value="{{ $interested->id }}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[sun_boiler]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[sun_boiler]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">

                    <div class="form-group add-space{{ $errors->has('house_ventilation') ? ' has-error' : '' }}">
                        <label for="house_ventilation" class=" control-label"><i data-toggle="collapse" data-target="#house-ventilation-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.house-ventilation.title')</label> <span>*</span>

                        <select required id="house_ventilation" class="form-control" name="house_ventilation" >
                            @foreach($houseVentilations as $houseVentilation)
                                <option @if($houseVentilation->id == old('house_ventilation')) selected @endif value="{{$houseVentilation->id}}">{{$houseVentilation->name}}</option>
                            @endforeach
                        </select>

                        <div id="house-ventilation-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('house_ventilation'))
                            <span class="help-block">
                        <strong>{{ $errors->first('house_ventilation') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space{{ $errors->has('interested[house_ventilation]') ? ' has-error' : '' }}">
                        <label for="house_ventilation" class="control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.interested')</label>

                        <select id="house_ventilation" class="form-control" name="interested[house_ventilation]" >
                            @foreach($isInterested as $interested)
                                <option @if($interested->id == old('interested[house_ventilation]')) selected @endif value="{{$interested->id}}">@lang($interested->translation_key)</option>
                            @endforeach
                        </select>

                        @if ($errors->has('interested[house_ventilation]'))
                            <span class="help-block">
                                <strong>{{ $errors->first('interested[house_ventilation]') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">

                    <div class="form-group add-space{{ $errors->has('house_ventilation_placed_date') ? ' has-error' : '' }}">
                        <label for="house_ventilation_placed_date" class=" control-label"><i data-toggle="collapse" data-target="#house-ventilation-placed-date-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.house-ventilation.if-mechanic')</label>

                        <input type="date" id="house_ventilation_placed_date" class="form-control" name="house_ventilation_placed_date" >

                        <div id="house-ventilation-placed-date-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('house_ventilation_placed_date'))
                            <span class="help-block">
                        <strong>{{ $errors->first('house_ventilation_placed_date') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('additional') ? ' has-error' : '' }}">
                        <label for="additional" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.energy-saving-measures.additional')</label>

                        <textarea id="additional" class="form-control" name="additional"> {{old('additional')}} </textarea>

                        <div id="additional-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('additional'))
                            <span class="help-block">
                        <strong>{{ $errors->first('additional') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <div id="data-about-usage">

            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.general-data.data-about-usage.title')</h4>
            <div class="row">
            <div class="col-sm-6">

                <div class="form-group add-space{{ $errors->has('total_citizens') ? ' has-error' : '' }}">
                    <label for="total_citizens" class=" control-label"><i data-toggle="collapse" data-target="#total-citizens-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.total-citizens')</label> <span>*</span>

                    <input type="text" id="total_citizens" class="form-control" value="{{old('total_citizens')}}" name="total_citizens" required>

                    <div id="total-citizens-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        And i would like to have it to...
                    </div>

                    @if ($errors->has('total_citizens'))
                        <span class="help-block">
                    <strong>{{ $errors->first('total_citizens') }}</strong>
                </span>
                    @endif
                </div>
            </div>

            <div class="col-sm-6">

                <div class="form-group add-space{{ $errors->has('cooked_on_gas') ? ' has-error' : '' }}">
                    <label for="cooked_on_gas" class=" control-label"><i data-toggle="collapse" data-target="#cooked-on-gas-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.cooked-on-gas')</label>
                    <label class="radio-inline">
                        <input type="radio" name="cooked_on_gas" value="1">@lang('woningdossier.cooperation.radiobutton.yes')
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="cooked_on_gas" value="2">@lang('woningdossier.cooperation.radiobutton.no')
                    </label>

                    <div id="cooked-on-gas-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        And i would like to have it to...
                    </div>

                    @if ($errors->has('cooked_on_gas'))
                        <span class="help-block">
                            <strong>{{ $errors->first('cooked_on_gas') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-sm-6">
                <div class="form-group add-space{{ $errors->has('thermostat_highest') ? ' has-error' : '' }}">
                    <label for="thermostat_highest" class=" control-label"><i data-toggle="collapse" data-target="#thermostat-highest-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.thermostat-highest')</label>

                    <input type="text" id="thermostat_highest" class="form-control" value="{{old('thermostat_highest')}}" name="thermostat_highest" >

                    <div id="thermostat-highest-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        And i would like to have it to...
                    </div>

                    @if ($errors->has('thermostat_highest'))
                        <span class="help-block">
                    <strong>{{ $errors->first('thermostat_highest') }}</strong>
                </span>
                    @endif
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group add-space{{ $errors->has('thermostat_lowest') ? ' has-error' : '' }}">
                    <label for="thermostat_lowest" class=" control-label"><i data-toggle="collapse" data-target="#thermostat-lowest-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.thermostat-lowest')</label>

                    <input id="thermostat_lowest" type="text" class="form-control" name="thermostat_lowest">

                    <div id="thermostat-lowest-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        And i would like to have it to...
                    </div>

                    @if ($errors->has('thermostat_lowest'))
                        <span class="help-block">
                            <strong>{{ $errors->first('thermostat_lowest') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('max_hours_thermostat_highest') ? ' has-error' : '' }}">
                        <label for="max_hours_thermostat_highest" class=" control-label"><i data-toggle="collapse" data-target="#max-hours-thermostat-highest-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.max-hours-thermostat-highest')</label>

                        <select id="max_hours_thermostat_highest" class="form-control" name="max_hours_thermostat_highest">
                            @for($hour = 0; $hour < 25; $hour++)
                                <option value="{{$hour}}">{{$hour}}</option>
                            @endfor
                                <option value="0">@lang('woningdossier.cooperation.radiobutton.not-important')</option>
                        </select>

                        <div id="max-hours-thermostat-highest-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>
                        @if ($errors->has('max_hours_thermostat_highest'))
                            <span class="help-block">
                                <strong>{{ $errors->first('max_hours_thermostat_highest') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('situation_first_floor') ? ' has-error' : '' }}">
                        <label for="situation_first_floor" class=" control-label"><i data-toggle="collapse" data-target="#situation-first-floor-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.situation-first-floor')</label>

                        <select id="situation_first_floor" class="form-control" name="situation_first_floor" >
                            @foreach($buildingHeatings as $buildingHeating)
                                <option @if($buildingHeating->id == old('situation_first_floor')) selected @endif value="{{ $buildingHeating->id}}">{{$buildingHeating->name}}</option>
                            @endforeach

                        </select>

                        <div id="situation-first-floor-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('situation_first_floor'))
                            <span class="help-block">
                        <strong>{{ $errors->first('situation_first_floor') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('situation_second_floor') ? ' has-error' : '' }}">
                        <label for="situation_second_floor" class=" control-label"><i data-toggle="collapse" data-target="#situation-second-floor-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.situation-second-floor')</label>

                        <select id="situation_second_floor" class="form-control" name="situation_second_floor" >
                            @foreach($buildingHeatings as $buildingHeating)
                                <option @if($buildingHeating->id == old('situation_second_floor')) selected @endif value="{{$buildingHeating->id}}">{{$buildingHeating->name}}</option>
                            @endforeach
                        </select>

                        <div id="situation-second-floor-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('situation_second_floor'))
                            <span class="help-block">
                        <strong>{{ $errors->first('situation_second_floor') }}</strong>
                    </span>
                        @endif
                    </div>
                </div>
         
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('comfortniveau_warm_tapwater') ? ' has-error' : '' }}">
                        <label for="comfortniveau_warm_tapwater" class=" control-label"><i data-toggle="collapse" data-target="#comfortniveau-warm-tapwater-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.comfortniveau-warm-tapwater')</label>

                        <select id="comfortniveau_warm_tapwater" class="form-control" name="comfortniveau_warm_tapwater" >
                            @foreach($comfortLevelsTapWater as $comfortLevelTapWater)
                                <option @if($comfortLevelTapWater->id == old('comfortniveau_warm_tapwater')) selected @endif value="{{$comfortLevelTapWater->id}}">{{$comfortLevelTapWater->name}}</option>
                            @endforeach
                        </select>
                        <div id="comfortniveau-warm-tapwater-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('comfortniveau_warm_tapwater'))
                            <span class="help-block">
                                <strong>{{ $errors->first('comfortniveau_warm_tapwater') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('electricity_consumption_past_year') ? ' has-error' : '' }}">
                        <label for="electricity_consumption_past_year" class=" control-label"><i data-toggle="collapse" data-target="#electricity-consumption-past-year-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.electricity-consumption-past-year')</label>

                        <input id="electricity_consumption_past_year" type="text" value="{{old('electricity_consumption_past_year')}}" class="form-control" name="electricity_consumption_past_year">

                        <div id="electricity-consumption-past-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('electricity_consumption_past_year'))
                            <span class="help-block">
                                <strong>{{ $errors->first('electricity_consumption_past_year') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('gas_usage_past_year') ? ' has-error' : '' }}">
                        <label for="gas_usage_past_year" class=" control-label"><i data-toggle="collapse" data-target="#gas-usage-past-year-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.gas-usage-past-year')</label>

                        <input id="gas_usage_past_year" type="text" value="{{old('gas_usage_past_year')}}" class="form-control" name="gas_usage_past_year">

                        <div id="gas-usage-past-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>


                        @if ($errors->has('gas_usage_past_year'))
                            <span class="help-block">
                                <strong>{{ $errors->first('gas_usage_past_year') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('additional_info') ? ' has-error' : '' }}">
                        <label for="additional-info" class=" control-label"><i data-toggle="collapse" data-target="#additional-info-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>@lang('woningdossier.cooperation.tool.general-data.data-about-usage.additional-info')</label>

                        <textarea id="additional-info" class="form-control" name="additional-info"> {{old('additional_info')}} </textarea>

                        <div id="additional-info-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('additional_info'))
                            <span class="help-block">
                                <strong>{{ $errors->first('additional_info') }}</strong>
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
    {{--<script>--}}
        {{--$(document).ready(function () {--}}

            {{--// select all select boxes--}}
            {{--$.each($('select').prev(), function(index, value) {--}}
                {{--// if the field is required skip it--}}
                {{--if($(this).text() == '*') {--}}
                    {{--var requiredSelectId = $(this).next().attr('id');--}}
                    {{--// just select the previous element--}}
                    {{--var requiredSelectLabel = $(this).prev();--}}
                    {{--// replace _ with ---}}
                    {{--var strippedSelectId = requiredSelectId.replace(/_/g, '-');--}}

                    {{--requiredSelectLabel.prepend('<i data-toggle="collapse" data-target="#'+strippedSelectId+'-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>');--}}


                {{--} else {--}}

                    {{--var selectId = $(this).next().attr('id');--}}

                    {{--var infoBoxId = selectId.replace(/_/g, '-');--}}

                    {{--$(this).prepend('<i data-toggle="collapse" data-target="#'+infoBoxId+'-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>');--}}
                {{--}--}}
            {{--});--}}

            {{--// select all input fields--}}
            {{--$.each($('input').prev(), function(index, value) {--}}
                {{--// if the field is we need to go one element back--}}
                {{--if($(this).text() == '*') {--}}

                    {{--// Get the id from the input--}}
                    {{--var requiredInputId = $(this).next().attr('id');--}}
                    {{--// replace the _ with ---}}
                    {{--var strippedInputId = requiredInputId.replace(/_/g, '-');--}}
                    {{--// just select the previous element (the label)--}}
                    {{--var requiredLabel = $(this).prev();--}}

                    {{--requiredLabel.prepend('<i data-toggle="collapse" data-target="#'+strippedInputId+'-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>');--}}

                {{--} else {--}}
                    {{--// get id--}}
                    {{--var inputId = $(this).next().attr('id');--}}
                    {{--// replace _ with ---}}
                    {{--var infoBoxId = inputId.replace(/_/g, '-');--}}

                    {{--$(this).prepend('<i data-toggle="collapse" data-target="#'+infoBoxId+'-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>');--}}
                {{--}--}}
            {{--});--}}
        {{--})--}}
    {{--</script>--}}
@endpush