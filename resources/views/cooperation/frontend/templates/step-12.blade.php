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
                            <span class="media-icon-wrapper">
                                <i class="icon-kitchen"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Keuken</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-bathroom" name="changes" value="bathroom">
                        <label for="changes-bathroom">
                            <span class="media-icon-wrapper">
                                <i class="icon-bathroom"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Badkamer</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-dormer" name="changes" value="dormer">
                        <label for="changes-dormer">
                            <span class="media-icon-wrapper">
                                <i class="icon-dormer"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Dakkapel</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-window-frame" name="changes" value="window-frame">
                        <label for="changes-window-frame">
                            <span class="media-icon-wrapper">
                                <i class="icon-window-frame"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Kozijnen</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-paint-job" name="changes" value="paint-job">
                        <label for="changes-paint-job">
                            <span class="media-icon-wrapper">
                                <i class="icon-paint-job"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Schilderwerk</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-sunroom" name="changes" value="sunroom">
                        <label for="changes-sunroom">
                            <span class="media-icon-wrapper">
                                <i class="icon-sunroom"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Serre</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper media-wrapper">
                        <input type="checkbox" id="changes-attic-room" name="changes" value="attic-room">
                        <label for="changes-attic-room">
                            <span class="media-icon-wrapper">
                                <i class="icon-attic-room"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>Kamer op zolder</span>
                        </label>
                    </div>
                    <div class="add-option-wrapper media-wrapper" x-data="modal()" >
                        <label for="add-option" x-on:click="toggle()">
                            <span class="media-icon-wrapper">
                                <i class="icon-plus-circle"></i>
                            </span>
                            <span>@lang('cooperation/frontend/tool.form.add-option')</span>
                        </label>
                        @component('cooperation.frontend.layouts.components.modal', ['header' => __('cooperation/frontend/tool.form.subject')])
                            <div class="flex flex-wrap mb-5">
                                <div class="form-group w-full -mt-4 mb-4">
                                    <input class="form-input" name="new_option[subject]" id="new-option-subject"
                                           placeholder="Placeholder">
                                </div>
                                <div class="w-full flex items-center">
                                    <i class="icon-sm icon-info mr-3"></i>
                                    <h6 class="heading-6">
                                        Prijsindicatie in €
                                    </h6>
                                </div>
                                <div class="form-group w-1/2 pr-1">
                                    <input class="form-input" name="new_option[price][from" id="new-option-price-from"
                                           placeholder="van">
                                </div>
                                <div class="form-group w-1/2 pl-1">
                                    <input class="form-input" name="new_option[price][to" id="new-option-price-to"
                                           placeholder="van">
                                </div>
                            </div>
                            <div class="w-full border border-gray fixed left-0"></div>
                            <div class="flex flex-wrap justify-center mt-14">
                                <button class="btn btn-purple w-full">
                                    <i class="icon-xs icon-plus-purple mr-3"></i>
                                    Voeg maatregel toe
                                </button>
                            </div>
                        @endcomponent
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
