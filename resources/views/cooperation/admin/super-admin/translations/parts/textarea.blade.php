@if($noWysiwyg)
    <textarea id="{{ $id }}"
              class="form-input question-input mb-0"
              name="{{ $htmlName }}"
    >{{$content}}</textarea>
@else
    <div class="w-full tiptap-container" x-data="tiptapEditor(@js($content))">
        @component('cooperation.layouts.components.tiptap')
            @slot('menuSlot')
                <button type="button" class="text"
                        x-on:click="$el.closest('.tiptap-parent').querySelector('.tiptap').editor.commands.setContent($el.closest('.tiptap-container').querySelector('.original-help-text').value)">
                    Herstel tekst
                </button>
            @endslot

            <textarea id="{{ $id }}"
                      class="form-input question-input" x-ref="editor"
                      name="{{ $htmlName }}"
            >{{$content}}</textarea>
        @endcomponent

        <input type="hidden" class="original-help-text"
               disabled="disabled" value="{{$content}}">
    </div>
@endif