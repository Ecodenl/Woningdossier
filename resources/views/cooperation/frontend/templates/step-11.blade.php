@extends('cooperation.frontend.layouts.app')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
        <div class="w-full">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => 'Welke zaken vindt u belangrijk?',
            ])
                @slot('modalBodySlot')
                    <p>
                        Wat vindt u nou echt belangrijk in uw huis?
                    </p>
                @endslot
                <div class="w-full grid grid-rows-3 grid-cols-2 grid-flow-row justify-items-center gap-x-32 lg:gap-x-64 gap-y-10">
                    @include('cooperation.frontend.layouts.parts.rating-slider', ['label' => 'Comfort'])
                    @include('cooperation.frontend.layouts.parts.rating-slider', ['label' => 'Duurzaamheid'])
                    @include('cooperation.frontend.layouts.parts.rating-slider', ['label' => 'Goede investering'])
                    @include('cooperation.frontend.layouts.parts.rating-slider', ['label' => 'Verlaging maandlasten'])
                    @include('cooperation.frontend.layouts.parts.rating-slider', ['label' => 'Naar eigen smaak maken'])
                    @include('cooperation.frontend.layouts.parts.rating-slider', ['label' => 'Gezond binnenklimaat'])
                </div>
            @endcomponent
        </div>
    </div>
    @include('cooperation.frontend.layouts.parts.step-buttons', [
        'current' => '11',
        'total' => '24',
    ])
@endsection