@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
        <div class="w-full flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full lg:w-1/2 lg:pr-3',
                'label' => 'Heeft u zonnepanelen?',
            ])
                @slot('modalBodySlot')
                    <p>
                        Heeft u zonnepanelen op uw dak liggen?
                    </p>
                @endslot
                <div class="w-full grid grid-rows-1 grid-cols-2 grid-flow-row justify-items-center">
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="solar-panels-yes" name="has_solar_panels" value="1">
                        <label for="solar-panels-yes">
                            <span class="media-icon-wrapper">
                                <i class="icon-solar-panels"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>@lang('default.yes')</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="solar-panels-no" name="has_solar_panels" value="0">
                        <label for="solar-panels-no">
                            <span class="media-icon-wrapper">
                                <i class="icon-solar-panels-none"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>@lang('default.no')</span>
                        </label>
                    </div>
                </div>
            @endcomponent
            <div class="w-full lg:w-1/2 lg:pl-3">
                @component('cooperation.frontend.layouts.components.form-group', [
                    'class' => 'form-group-heading w-full',
                    'label' => 'Hoeveel zonnepanelen?',
                    'id' => 'solar-panel-count',
                    'inputName' => 'solar_panel_count',
                ])
                    @slot('modalBodySlot')
                        <p>
                            Als u het niet weet, bel dan het bedrijf dat ze geplaatst heeft. Zij hebben vast nog een factuur waarop staat hoeveel panelen er zijn geplaatst.
                            <span class="font-bold">Ga absoluut niet zelf het dak op om ze te tellen.</span>
                        </p>
                    @endslot
                    <input class="form-input" name="solar_panel_count" id="solar-panel-count">
                @endcomponent
                @component('cooperation.frontend.layouts.components.form-group', [
                    'class' => 'form-group-heading w-full',
                    'label' => 'Piekvermogen per paneel?',
                    'id' => 'solar-panel-power',
                    'inputName' => 'solar_panel_power',
                ])
                    @slot('modalBodySlot')
                        <p>
                            Hoeveel stroom leveren uw panelen in maximum vermogen?
                        </p>
                    @endslot
                    <input class="form-input" name="solar_panel_power" id="solar-panel-power">
                    <div class="input-group-append">
                        Wp
                    </div>
                @endcomponent
            </div>
        </div>
        <div class="w-full pt-5">
            @component('cooperation.frontend.layouts.components.form-group', [
                    'class' => 'form-group-heading w-full',
                    'label' => 'Wanneer zijn de zonnepalen geplaats?',
                    'id' => 'solar-panel-date',
                    'inputName' => 'solar_panel_date',
                ])
                @slot('modalBodySlot')
                    <p>
                        Kijk nog is op de factuur.
                    </p>
                @endslot
                <div class="w-1/2">
                    <input class="form-input" name="solar_panel_date" id="solar-panel-date" type="text"
                           placeholder="Voer jaartal in">
                </div>
            @endcomponent
        </div>
    </div>
    @include('cooperation.frontend.layouts.parts.step-buttons', [
        'current' => '23',
        'total' => '24',
    ])
@endsection