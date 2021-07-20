@extends('cooperation.frontend.layouts.app')

@section('content')
    <div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
        <div class="w-full space-x-3">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => 'Welke zaken zou u willen veranderen aan uw woning?',
            ])
                @slot('modalBodySlot')
                    <p>
                        Selecteer welke dingen u zou willen veranderen aan uw woning.
                    </p>
                @endslot
                <div class="w-full grid grid-rows-2 grid-cols-4 grid-flow-row justify-items-center">
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-kitchen" name="changes" value="kitchen">
                        <label for="changes-kitchen">
                            <i class="icon-kitchen"></i>
                            <span class="checkmark"></span>
                            <span>Keuken</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-bathroom" name="changes" value="bathroom">
                        <label for="changes-bathroom">
                            <i class="icon-bathroom"></i>
                            <span class="checkmark"></span>
                            <span>Badkamer</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-dormer" name="changes" value="dormer">
                        <label for="changes-dormer">
                            <i class="icon-dormer"></i>
                            <span class="checkmark"></span>
                            <span>Dakkapel</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-window-frame" name="changes" value="window-frame">
                        <label for="changes-window-frame">
                            <i class="icon-window-frame"></i>
                            <span class="checkmark"></span>
                            <span>Kozijnen</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-paint-job" name="changes" value="paint-job">
                        <label for="changes-paint-job">
                            <i class="icon-paint-job"></i>
                            <span class="checkmark"></span>
                            <span>Schilderwerk</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-sunroom" name="changes" value="sunroom">
                        <label for="changes-sunroom">
                            <i class="icon-sunroom"></i>
                            <span class="checkmark"></span>
                            <span>Serre</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-attic-room" name="changes" value="attic-room">
                        <label for="changes-attic-room">
                            <i class="icon-attic-room"></i>
                            <span class="checkmark"></span>
                            <span>Kamer op zolder</span>
                        </label>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    @include('cooperation.frontend.layouts.parts.step-buttons', [
        'current' => '12',
        'total' => '24',
    ])
@endsection