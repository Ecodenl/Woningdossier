<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">
    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'form-group-heading',
        'label' => "Welke zaken zou u willen veranderen aan uw woning",
        'withInputSource' => false,
    ])
        @slot('modalBodySlot')
            <p>
                bubbas
            </p>
        @endslot

        @livewire('cooperation.frontend.tool.quick-scan.custom-changes')
    @endcomponent
</div>
