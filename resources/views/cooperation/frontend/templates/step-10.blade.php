@extends('cooperation.frontend.layouts.app')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
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
                @include('cooperation.frontend.layouts.parts.slider')
            @endcomponent
        </div>
    </div>
    @include('cooperation.frontend.layouts.parts.step-buttons', [
        'current' => '10',
        'total' => '24',
    ])
@endsection