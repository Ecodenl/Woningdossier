<div>
    <div class="w-full flex flex-wrap bg-blue-100 pb-8 px-3 lg:px-8"
         x-data="adaptiveInputs(128)" {{-- 128px === 8rem, default height for textareas --}}>
        @php
            $disableResident = \App\Helpers\HoomdossierSession::isUserObserving() || $currentInputSource->short !== $residentInputSource->short;
            $disableCoach = \App\Helpers\HoomdossierSession::isUserObserving() || $currentInputSource->short !== $coachInputSource->short;
        @endphp
        @component('cooperation.frontend.layouts.components.form-group', [
            'label' => __('cooperation/frontend/tool.my-plan.comments.resident'),
            'class' => 'w-full md:w-1/2 md:pr-3',
            'withInputSource' => false,
            'id' => 'comments-resident',
            'inputName' => 'comments.resident'
        ])
            <div x-data="modal()" class="w-full">
                <textarea id="comments-resident" class=" w-full form-input" wire:model="residentCommentText"
                      @if($disableResident) disabled @endif x-bind="typable" wire:ignore
                          x-on:click="toggle()"
                      placeholder="@lang('default.form.input.comment-placeholder')"></textarea>

                @component('cooperation.frontend.layouts.components.modal', ['id' => $modalId ?? '',])
                    <textarea class="w-full form-input" wire:model="residentCommentText"></textarea>

                    @slot('header')
                        @lang('cooperation/frontend/tool.my-plan.comments.resident')
                    @endslot
                    <div class="flex justify-end space-x-2">
                        <button class="btn btn-orange" wire:click="resetComment()" x-on:click="close()">Annuleren</button>
                        <button class="btn btn-purple"
                                @if($disableResident) disabled @endif
                                wire:click="save('{{\App\Models\InputSource::RESIDENT_SHORT}}')"
                                wire:loading.attr="disabled"
                                x-on:click="close()"
                                wire:target="saveComment">
                            @lang('default.buttons.save')
                        </button>
                    </div>
                @endcomponent
            </div>
        @endcomponent
        @component('cooperation.frontend.layouts.components.form-group', [
            'label' => __('cooperation/frontend/tool.my-plan.comments.coach'),
            'class' => 'w-full md:w-1/2 md:pl-3',
            'withInputSource' => false,
            'id' => 'comments-coach',
            'inputName' => 'comments.coach'
        ])
            @slot('header')
                @lang('cooperation/frontend/tool.my-plan.comments.coach')
            @endslot
            <div x-data="modal()" class="w-full">
            <textarea id="comments-coach" class="w-full form-input" wire:model="coachCommentText"
                      @if($disableCoach) disabled @endif x-bind="typable" wire:ignore x-on:click="open()"
                      placeholder="@lang('default.form.input.comment-placeholder')"></textarea>
                @component('cooperation.frontend.layouts.components.modal', ['id' => $modalId ?? ''])
                    <textarea class="w-full form-input" wire:model="coachCommentText"></textarea>

                    <div class="flex justify-end space-x-2">
                        <button class="btn btn-orange" wire:click="resetComment()" x-on:click="close()">Annuleren</button>
                    <button class="btn btn-purple"
                            @if($disableCoach) disabled @endif
                            wire:click="save('{{\App\Models\InputSource::COACH_SHORT}}')"
                            wire:loading.attr="disabled"
                            wire:target="saveComment"
                            x-on:click="close()">
                        @lang('default.buttons.save')
                    </button>
                @endcomponent
            </div>
        @endcomponent
    </div>
</div>
