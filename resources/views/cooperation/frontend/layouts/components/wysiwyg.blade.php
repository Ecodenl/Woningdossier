<div class="tiny-editor relative w-inherit flex" @if(! empty($attr)) {!! $attr !!} @endif
     wire:ignore>
    {{ $slot }}
</div>

{{-- TODO: Use pushonce in L9 --}}
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            tinymce.init({
                selector: '.tiny-editor textarea',
                // menubar: 'edit format',
                menubar: false, // Bar above the toolbar with advanced options
                statusbar: true, // Bar that shows the current HTML tag, word count, etc. at the bottom of the editor
                plugins: [
                    'link', // https://www.tiny.cloud/docs/tinymce/6/link/
                    'wordcount', // https://www.tiny.cloud/docs/tinymce/6/wordcount/
                ],
                // Link plugin settings start
                link_default_target: '_blank',
                link_target_list: false,
                link_title: false,
                // Link plugin settings end
                toolbar: 'link bold italic underline strikethrough',
                promotion: false,
                language: 'nl',
                resize: false,
                height: 200,
                content_css: '{{ asset('css/frontend/tinymce.css') }}',
                setup: (editor) => {
                    editor.on('click', (event) => {
                        {{-- This is purely for the popup textareas... --}}
                        window.triggerEvent(editor.targetElm.closest('.tiny-editor'), 'click');
                    });
                    editor.on('change', (event) => {
                        // Save editor (to textarea), then trigger change (to update Livewire).
                        editor.save();
                        window.triggerEvent(editor.targetElm, 'change');

                        // Okay, hear me out: Due to the popup, we have 2 tiny editors that are meant to be in sync.
                        // Instead of waiting for a full server request circle and detecting the textarea changing
                        // values, and potentially overriding any user input, we instead just manually update the
                        // tiny editor. This is faster, more reliable and less junky for the user.
                        if (! editor.id.startsWith('clickable-')) {
                            tinymce.get(`clickable-${editor.id}`).setContent(editor.getContent());
                        }
                    });
                    // Reset tiny if related textarea is reset
                    document.addEventListener('reset-question', (event) => {
                        if (editor.id.includes(event.detail.short)) {
                            editor.setContent(editor.targetElm.value);
                        }
                    });
                }
            });
        });
    </script>
@endpush