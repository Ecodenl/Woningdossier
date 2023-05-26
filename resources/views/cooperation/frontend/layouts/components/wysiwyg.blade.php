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
                {{-- This is purely for the popup textareas... --}}
                setup: (editor) => {
                    editor.on('click', (e) => {
                        window.triggerEvent(editor.targetElm.closest('.tiny-editor'), 'click');
                    });
                }
            });
        });
    </script>
@endpush