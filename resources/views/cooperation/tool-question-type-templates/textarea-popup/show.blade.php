<div x-data="modal()" class="w-full flex">

    <textarea wire:model.lazy="filledInAnswers.{{$toolQuestion['short']}}"
              id="{{$toolQuestion->short}}"
              class="form-input"
              wire:ignore x-on:click="toggle()"
              placeholder="{{$toolQuestion->placeholder}}"
              @if(($disabled ?? false)) disabled @endif>
    </textarea>

    @component('cooperation.frontend.layouts.components.modal', ['class' => 'w-full md:w-1/2'])
        <textarea wire:model.lazy="filledInAnswers.{{$toolQuestion['short']}}"
                  id="{{$toolQuestion->short}}"
                  class="form-input w-full"
                  placeholder="{{$toolQuestion->placeholder}}"
        ></textarea>

        @slot('header')
            {{ $toolQuestion->name }}
        @endslot
        <div class="flex justify-end space-x-2">
            <button class="btn btn-orange" wire:click="resetToOriginalAnswer('{{$toolQuestion['short']}}')"
                    x-on:click="close()">
                @lang('default.buttons.cancel')
            </button>
            <button class="btn btn-purple" wire:click="saveSpecificToolQuestion('{{$toolQuestion['short']}}')"
                    x-on:click="close()">
                @lang('default.buttons.save')
            </button>
        </div>
    @endcomponent
</div>