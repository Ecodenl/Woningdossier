@extends('cooperation.frontend.layouts.app')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20">
        <div class="w-full space-x-3">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading mb-5',
                'label' => 'Hoe wordt er gekookt?',
            ])
                @slot('modalBodySlot')
                    <p>
                        Selecteer de wijze waarbij u thuis kookt.
                    </p>
                @endslot
                <div class="flex flex-wrap justify-between w-full">
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="cooking-type-gas" name="cooking_type" value="gas">
                        <label for="cooking-type-gas">
                            <i class="icon-gas"></i>
                            <span class="checkmark"></span>
                            <span>Gas</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="cooking-type-electric" name="cooking_type" value="electric">
                        <label for="cooking-type-electric">
                            <i class="icon-electric"></i>
                            <span class="checkmark"></span>
                            <span>Elektrisch</span>
                        </label>
                    </div>
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio" id="cooking-type-induction" name="cooking_type" value="induction">
                        <label for="cooking-type-induction">
                            <i class="icon-induction"></i>
                            <span class="checkmark"></span>
                            <span>Inductie</span>
                        </label>
                    </div>
                    <div><!-- Force justify between --></div>
                </div>
            @endcomponent
        </div>
        <div class="w-full">

        </div>
    </div>
@endsection