@extends('cooperation.frontend.layouts.app')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-10">
        <div class="w-full">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => 'Temperatuur van de thermostaat op de <span class="text-green">hoge</span> stand',
            ])
                @slot('modalBodySlot')
                    <p>
                        Hoe hoog staat uw thermostaat?
                    </p>
                @endslot
                @include('cooperation.frontend.layouts.parts.slider', ['min' => 10, 'max' => 30, 'unit' => '°', 'step' => 10])
            @endcomponent
        </div>
        <div class="w-full pt-10">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => 'Temperatuur van de thermostaat op de <span class="text-green">lage</span> stand',
            ])
                @slot('modalBodySlot')
                    <p>
                        Hoe laag staat uw thermostaat?
                    </p>
                @endslot
                @include('cooperation.frontend.layouts.parts.slider', ['min' => 10, 'max' => 30, 'unit' => '°'])
            @endcomponent
        </div>
        <div class="w-full pt-10">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => 'Hoeveel uur per dag staat de thermostaat op de <span class="text-green">hoge</span> stand',
            ])
                @slot('modalBodySlot')
                    <p>
                        Hoe lang staat uw thermostaat dagelijks aan?
                    </p>
                @endslot
                @include('cooperation.frontend.layouts.parts.slider', ['min' => 0, 'max' => 24])
            @endcomponent
        </div>
    </div>
    @include('cooperation.frontend.layouts.parts.step-buttons', [
        'current' => '07',
        'total' => '24',
    ])
@endsection