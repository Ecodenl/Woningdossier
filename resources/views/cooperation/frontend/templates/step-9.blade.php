@extends('cooperation.frontend.layouts.app')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
        <div class="w-full">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => 'Hoe wordt er gekookt?',
            ])
                @slot('modalBodySlot')
                    <p>
                        Selecteer de wijze waarbij u thuis kookt.
                    </p>
                @endslot
                <div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row justify-items-center">
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="cooking-type-gas" name="cooking_type" value="gas">
                        <label for="cooking-type-gas">
                            <span class="media-icon-wrapper">
                                <i class="icon-gas"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Gas</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="cooking-type-electric" name="cooking_type" value="electric">
                        <label for="cooking-type-electric">
                            <span class="media-icon-wrapper">
                                <i class="icon-electric"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Elektrisch</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="cooking-type-induction" name="cooking_type" value="induction">
                        <label for="cooking-type-induction">
                            <span class="media-icon-wrapper">
                                <i class="icon-induction"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Inductie</span>
                        </label>
                    </div>
                </div>
            @endcomponent
        </div>
        <div class="w-full flex flex-wrap pt-5">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full lg:w-1/2 lg:pr-3',
                'label' => 'Wat is het gasgebruik?'
            ])
                @slot('modalBodySlot')
                    <p>
                        Vul (in kubieke meters) in hoeveel gas u gebruikt per jaar.
                    </p>
                @endslot
                <input class="form-input" name="usage_gas" id="usage-gas">
                <div class="input-group-append">
                    m<sup>3</sup>
                </div>
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
               'class' => 'form-group-heading w-full lg:w-1/2 lg:pl-3',
               'label' => 'Wat is het elektragebruik?'
            ])
                @slot('modalBodySlot')
                    <p>
                        Vul (in kilowattuur) in hoeveel stroom u gebruikt per jaar.
                    </p>
                @endslot
                <input class="form-input" name="usage_electric" id="usage-electric">
                <div class="input-group-append">
                    kWh
                </div>
            @endcomponent
        </div>
    </div>
    @include('cooperation.frontend.layouts.parts.step-buttons', [
        'current' => '09',
        'total' => '24',
    ])
@endsection