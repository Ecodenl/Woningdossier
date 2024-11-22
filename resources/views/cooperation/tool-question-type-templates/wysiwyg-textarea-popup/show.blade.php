<div x-data="modal()" class="w-full flex">
    @component('cooperation.frontend.layouts.components.wysiwyg', [
        'attr' => 'x-on:click="toggle()"',
        'disabled' => $disabled ?? false,
        'withScript' => false,
    ])
        <textarea wire:model.blur.defer="filledInAnswers.{{$toolQuestion->short}}"
                  id="clickable-{{$toolQuestion->short}}" wire:ignore
                  class="form-input"
                  placeholder="{{$toolQuestion->placeholder}}"
                  @if(($disabled ?? false)) disabled @endif>
        </textarea>
    @endcomponent

    @component('cooperation.frontend.layouts.components.modal', ['class' => 'w-full md:w-1/2'])
        @component('cooperation.frontend.layouts.components.wysiwyg', [
            'disabled' => $disabled ?? false,
            'withScript' => false,
        ])
            <textarea wire:model.blur.defer="filledInAnswers.{{$toolQuestion->short}}"
                  id="{{$toolQuestion->short}}"
                  class="form-input w-full"
                  placeholder="{{$toolQuestion->placeholder}}"
                  @if(($disabled ?? false)) disabled @endif
            ></textarea>
        @endcomponent

        @slot('header')
            {{ $toolQuestion->name }}
        @endslot
        <div class="flex justify-end space-x-2">
            <button class="btn btn-orange" wire:click="resetToOriginalAnswer('{{$toolQuestion->short}}')"
                    x-on:click="close()">
                @lang('default.buttons.cancel')
            </button>
            <button class="btn btn-purple" wire:click="saveSpecificToolQuestion('{{$toolQuestion->short}}')"
                    x-on:click="close()">
                @lang('default.buttons.save')
            </button>
        </div>
    @endcomponent
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initTinyMCE({
                content_css: '{{ asset('css/frontend/tinymce.css') }}',
                setup: (editor) => {
                    // Click doesn't trigger when the editor is readonly. This is fine.
                    editor.on('click', (event) => {
                        // This is purely for the popup textareas...
                        window.triggerEvent(editor.targetElm.closest('.tiny-editor'), 'click');
                    });
                    editor.on('change', (event) => {
                        // Okay, hear me out: Due to the popup, we have 2 tiny editors that are meant to be in sync.
                        // Instead of waiting for a full server request circle and detecting the textarea changing
                        // values, and potentially overriding any user input, we instead just manually update the
                        // tiny editor. This is faster, more reliable and less junky for the user.
                        if (! editor.id.startsWith('clickable-')) {
                            tinymce.get(`clickable-${editor.id}`).setContent(editor.getContent());
                        }
                    });
                },
            });
        });
    </script>
@endpush