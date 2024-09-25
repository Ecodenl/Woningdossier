@php
    $disabled ??= false;
@endphp

<div class="tiny-editor relative w-inherit flex" @if(! empty($attr)) {!! $attr !!} @endif
     wire:ignore>
    {{ $slot }}
</div>

@if(($withScript ?? true))
    @once
        @push('js')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    initTinyMCE({
                        content_css: '{{ asset('css/frontend/tinymce.css') }}',
                    });
                });
            </script>
        @endpush
    @endonce
@endif