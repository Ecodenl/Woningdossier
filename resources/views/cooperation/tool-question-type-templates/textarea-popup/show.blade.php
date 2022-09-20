<div x-data="modal()" class="w-full flex">

    <textarea wire:model="filledInAnswers.{{$toolQuestion['id']}}"
              id="{{$toolQuestion->short}}"
              class="form-input"
              wire:ignore x-on:click="toggle()"
              placeholder="{{$toolQuestion->placeholder}}"
              @if(($disabled ?? false)) disabled @endif>
    </textarea>

    @component('cooperation.frontend.layouts.components.modal', ['class' => 'w-full md:w-1/2'])
        <textarea wire:model="filledInAnswers.{{$toolQuestion['id']}}"
                  id="{{$toolQuestion->short}}"
                  class="form-input w-full"
                  placeholder="{{$toolQuestion->placeholder}}"
        ></textarea>

        @slot('header')
            @php $inputSource = \App\Helpers\HoomdossierSession::getInputSource(true) @endphp
            @lang("cooperation/frontend/tool.my-plan.comments.{$inputSource->short}")
        @endslot
        <div class="flex justify-end space-x-2">
            <button class="btn btn-orange" wire:click="resetToOriginalAnswer({{$toolQuestion['id']}})"
                    x-on:click="close()">
                Annuleren
            </button>
            <button class="btn btn-purple" wire:click="saveSpecificToolQuestion({{$toolQuestion['id']}})"
                    x-on:click="close()">
                @lang('default.buttons.save')
            </button>
        </div>
    @endcomponent
</div>