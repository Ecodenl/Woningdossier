<div x-data="modal()" class="w-full flex">
    @component('cooperation.frontend.layouts.components.wysiwyg', [
        'attr' => 'x-on:click="toggle()"',
        'disabled' => $disabled ?? false,
    ])
        <textarea wire:model.lazy="filledInAnswers.{{$toolQuestion['short']}}"
                  id="clickable-{{$toolQuestion->short}}" wire:ignore
                  class="form-input"
                  placeholder="{{$toolQuestion->placeholder}}"
                  @if(($disabled ?? false)) disabled @endif>
        </textarea>
    @endcomponent

    @component('cooperation.frontend.layouts.components.modal', ['class' => 'w-full md:w-1/2'])
        @component('cooperation.frontend.layouts.components.wysiwyg', [
            'disabled' => $disabled ?? false,
        ])
            <textarea wire:model.lazy="filledInAnswers.{{$toolQuestion['short']}}"
                  id="{{$toolQuestion->short}}"
                  class="form-input w-full"
                  placeholder="{{$toolQuestion->placeholder}}"
                  @if(($disabled ?? false)) disabled @endif
            ></textarea>
        @endcomponent

        @slot('header')
            @php $inputSource = \App\Helpers\HoomdossierSession::getInputSource(true) @endphp
            @lang("cooperation/frontend/tool.my-plan.comments.{$inputSource->short}")
        @endslot
        <div class="flex justify-end space-x-2">
            <button class="btn btn-orange" wire:click="resetToOriginalAnswer('{{$toolQuestion['short']}}')"
                    x-on:click="close()">
                Annuleren
            </button>
            <button class="btn btn-purple" wire:click="saveSpecificToolQuestion('{{$toolQuestion['short']}}')"
                    x-on:click="close()">
                @lang('default.buttons.save')
            </button>
        </div>
    @endcomponent
</div>