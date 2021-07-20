@extends('cooperation.frontend.layouts.app')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
        <div class="w-full">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => 'Wat gebruikt u voor de verwarming en warm water?',
            ])
                @slot('modalBodySlot')
                    <p>
                        Selecteer de wijze waarbij u thuis het huis en het water verwarmt.
                    </p>
                @endslot
                <div class="w-full grid grid-rows-2 grid-cols-4 grid-flow-row justify-items-center">
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="heating-type-central-heater-gas" name="heating_type" value="central-heater-gas">
                        <label for="heating-type-central-heater-gas">
                            <span class="media-icon-wrapper">
                                <i class="icon-central-heater-gas"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Gasketel</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="heating-type-heat-pump" name="heating_type" value="heat-pump">
                        <label for="heating-type-heat-pump">
                            <span class="media-icon-wrapper">
                                <i class="icon-heat-pump"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Warmtepomp</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="heating-type-infrared" name="heating_type" value="infrared">
                        <label for="heating-type-infrared">
                            <span class="media-icon-wrapper">
                                <i class="icon-infrared-heater"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Infrarood</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="heating-type-city-heating" name="heating_type" value="city-heating">
                        <label for="heating-type-city-heating">
                            <span class="media-icon-wrapper">
                                <i class="icon-city-heating"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Stadsverwarming</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="heating-type-other" name="heating_type" value="other">
                        <label for="heating-type-other">
                            <span class="media-icon-wrapper">
                                <i class="icon-other"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>@lang('cooperation/frontend/tool.form.other')...</span>
                        </label>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    @include('cooperation.frontend.layouts.parts.step-buttons', [
        'current' => '18',
        'total' => '24',
    ])
@endsection