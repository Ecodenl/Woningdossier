<div class="w-full" x-data="tiptapEditor($wire.entangle('filledInAnswers.{{$toolQuestion->short}}'))" wire:ignore>
    @component('cooperation.layouts.components.tiptap')
        <textarea wire:model.blur="filledInAnswers.{{$toolQuestion->short}}" id="{{$toolQuestion->short}}"
                  class="form-input" placeholder="{{$toolQuestion->placeholder}}"
                  x-ref="editor"
                  @if(($disabled ?? false))
                      disabled
                  @else
                      x-on:input-updated.window="$el.setAttribute('disabled', true);"
                      x-on:input-update-processed.window="$el.removeAttribute('disabled');"
                  @endif
        ></textarea>
    @endcomponent
</div>