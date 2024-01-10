@php
    $disabled ??= false;
@endphp

<div class="tiny-editor relative w-inherit flex" @if(! empty($attr)) {!! $attr !!} @endif
     wire:ignore>
    {{ $slot }}
</div>

{{-- TODO: Use pushonce in L9 --}}
@if(($withScript ?? true))
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                initTinyMCE({
                    content_css: '{{ asset('css/frontend/tinymce.css') }}',
                });
            });
        </script>
    @endpush
@endif