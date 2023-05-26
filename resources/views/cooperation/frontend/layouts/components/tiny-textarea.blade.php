<div class="tiny-editor relative w-inherit flex">
    {{ $slot }}
</div>

{{-- TODO: Use pushonce in L9 --}}
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            tinymce.init({
                selector: '.tiny-editor textarea',
                // menubar: 'edit format',
                menubar: '',
                plugins: 'link',
                toolbar: 'link bold italic underline strikethrough',
                promotion: false,
                language: 'nl',
                resize: false,
                height: 200,
                content_css: '{{ asset('css/frontend/tinymce.css') }}',
            });
        });
    </script>
@endpush