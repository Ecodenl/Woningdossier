@extends('cooperation.frontend.layouts.tool')

@section('page_title', 'Hoomdossier input guide')

@section('content')
    @php
        $html = '<li class="change-input-value" data-input-value="2" data-input-source-short="resident">Bewoner: N/A</li><li class="change-input-value" data-input-value="1" data-input-source-short="coach">Coach: N/A</li>';
    @endphp


    <div class="w-full">
        <h4 class="heading-4">
            Welke woning past het beste bij uw situatie?
        </h4>
        @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Voorbeeldwoning opties'])
            @slot('sourceSlot')
                {!! $html !!}
            @endslot
            @slot('modalBodySlot')
                <p class="font-bold text-red">
                    Use @@slot('modalBodySlot') within a form group component to set the modal content in the modal
                    body.
                </p>
                <br>
                <p class="font-bold">div.radio-wrapper > input type="radio" ~ label > span.checkmark ~ span</p>
                <p class="font-bold text-red">
                    Note: Each group of elements (e.g. radios, a text input) is wrapped in a form component:
                    <br>
                    @@component('cooperation.frontend.layouts.components.form-group')
                </p>
            @endslot
            <div class="radio-wrapper pr-3">
                <input type="radio" id="example-building-1" name="example-building" value="1">
                <label for="example-building-1">
                    <span class="checkmark"></span>
                    <span>Een jaren vijftig woning</span>
                </label>
            </div>
            <div class="radio-wrapper pl-3">
                <input type="radio" id="example-building-2" name="example-building" value="2" checked>
                <label for="example-building-2">
                    <span class="checkmark"></span>
                    <span>Een jaren zestig woning</span>
                </label>
            </div>
            <div class="radio-wrapper pr-3">
                <input type="radio" id="example-building-3" name="example-building" value="3">
                <label for="example-building-3">
                    <span class="checkmark"></span>
                    <span>Een rijtjeshuis</span>
                </label>
            </div>
            <div class="radio-wrapper pl-3">
                <input type="radio" id="example-building-4" name="example-building" value="4">
                <label for="example-building-4">
                    <span class="checkmark"></span>
                    <span>Een nieuwbouw woning</span>
                </label>
            </div>
        @endcomponent
    </div>

    <div class="w-full">
        <div class="flex items-center justify-between w-full">
            <h4 class="heading-4">
                Woninggegevens
            </h4>
            <button class="btn btn-purple">
                Opslaan
            </button>
        </div>

        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pr-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @slot('modalBodySlot')
                    <p class="font-bold text-red">
                        Use @@slot('modalBodySlot') within a form group component to set the modal content in the modal
                        body.
                    </p>
                    <br>
                    <p class="font-bold">input.form-input</p>
                @endslot
                <input id="default" type="text" class="form-input" placeholder="Placeholder">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pl-3'])
                    @slot('sourceSlot')
                        {!! $html !!}
                    @endslot
                @slot('modalBodySlot')
                    <p class="font-bold">select.form-input (class for styling in case of errors with Alpine)</p>
                    <p class="font-bold text-red">
                        Note: The select should be wrapped by the Alpine select:
                        <br>
                        @@component('cooperation.frontend.layouts.components.alpine-select')
                    </p>
                @endslot
                @component('cooperation.frontend.layouts.components.alpine-select')
                    <select id="dropdown" class="form-input" name="alpine[dropdown]">
                        <option selected disabled>Dropdown</option>
                    </select>
                @endcomponent
            @endcomponent
        </div>
        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pr-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                <input id="default-2" type="text" class="form-input" placeholder="Placeholder">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pl-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @component('cooperation.frontend.layouts.components.alpine-select', ['initiallyOpen' => true])
                    <select id="dropdown-open" class="form-input" name="alpine[dropdown_open]">
                        <option selected disabled>Dropdown focus</option>
                        <option>Eerste</option>
                        <option selected>Tweede</option>
                        <option>Derde</option>
                        <option>Vierde</option>
                        <option>Vijfde</option>
                    </select>
                @endcomponent
            @endcomponent
        </div>
        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3 form-error required'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @slot('modalBodySlot')
                    <p class="font-bold">Error styling handled by form group component</p>
                @endslot
                <input id="error" type="text" class="form-input" placeholder="Error">
                <p class="form-error-label w-full">
                    Vul de correcte gegevens in
                </p>
            @endcomponent
        </div>
        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3 form-error required'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                <input id="input-group-error" type="text" class="form-input" placeholder="Error met waarde">
                <div class="input-group-append">
                    m<sup>2</sup>
                </div>
                <p class="form-error-label">
                    Waarde moet een nummer zijn
                </p>
            @endcomponent
        </div>
        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @slot('modalBodySlot')
                    <p class="font-bold">input.form-input:disabled</p>
                @endslot
                <input id="disabled" type="text" class="form-input" placeholder="Disabled" disabled>
            @endcomponent
        </div>
        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3 required'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @slot('modalBodySlot')
                    <p class="font-bold text-red">
                        Append .required to component:
                        <br>
                        @@component('cooperation.frontend.layouts.components.form-group', ['class' => 'required'])
                    </p>
                @endslot
                <input id="required" type="text" class="form-input" placeholder="Verplicht">
            @endcomponent
        </div>

        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pr-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @slot('modalBodySlot')
                    <p class="font-bold">textarea.form-input</p>
                @endslot
                <textarea id="text-area" class="form-input" placeholder="Text area"></textarea>
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pl-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                <input id="placeholder" type="text" class="form-input" placeholder="Placeholder">
            @endcomponent
        </div>
    </div>

    <div class="w-full">
        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pr-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @slot('modalBodySlot')
                    <p class="font-bold">input.form-input ~ div.input-group-append</p>
                @endslot
                <input type="text" class="form-input" placeholder="Placeholder">
                <div class="input-group-append">
                    m<sup>2</sup>
                </div>
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pl-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @slot('modalBodySlot')
                    <p class="font-bold text-red">
                        Append .icon to component:
                        <br>
                        @@component('cooperation.frontend.layouts.components.alpine-select', ['icon' => 'icon-detached-house'])
                    </p>
                @endslot
                @component('cooperation.frontend.layouts.components.alpine-select', ['icon' => 'icon-detached-house'])
                    <select id="dropdown-icon" class="form-input" name="alpine[dropdown_icon]">
                        <option selected disabled>Placeholder icon</option>
                    </select>
                @endcomponent
            @endcomponent
        </div>
        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                <input type="text" class="form-input" placeholder="Placeholder">
                <div class="input-group-append">
                    kWh
                </div>
            @endcomponent
        </div>
    </div>

    <div class="w-full">
        <div class="flex flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full'])
                @slot('sourceSlot')
                    {!! $html !!}
                @endslot
                @slot('modalBodySlot')
                    <p class="font-bold">div.checkbox-wrapper > input type="checkbox" ~ label > span.checkmark ~ span</p>
                @endslot
                <div class="checkbox-wrapper pr-3">
                    <input type="checkbox" id="subject-1" name="subject" value="1">
                    <label for="subject-1">
                        <span class="checkmark"></span>
                        <span>Uitleg gegeven</span>
                    </label>
                </div>
                <div class="checkbox-wrapper pr-3">
                    <input type="checkbox" id="subject-2" name="subject" value="2" checked>
                    <label for="subject-2">
                        <span class="checkmark"></span>
                        <span>Interesse in maatregel</span>
                    </label>
                </div>
                <div class="checkbox-wrapper pr-3">
                    <input type="checkbox" id="subject-3" name="subject" value="3">
                    <label for="subject-3">
                        <span class="checkmark"></span>
                        <span>Uitgevoerd</span>
                    </label>
                </div>
                <div class="checkbox-wrapper pr-3">
                    <input type="checkbox" id="subject-4" name="subject" value="4" disabled>
                    <label for="subject-4">
                        <span class="checkmark"></span>
                        <span>Disabled</span>
                    </label>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="w-full">
        <div class="flex items-center justify-end w-full">
            <button class="btn btn-purple">
                Opslaan
            </button>
        </div>
    </div>

    {{-- White space --}}
    <div class="flex w-full h-10"></div>
@endsection