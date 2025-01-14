{{-- TODO: Move to cooperation/layouts --}}
<div id="{{ $id ?? '' }}" class="modal-container" x-show="opened" x-cloak wire:ignore.self
     @if(! empty($attr)) {!! $attr !!} @endif
     x-on:keydown.escape.window="if (! document.activeElement.closest('.tox-dialog')) { close(); }"
     x-on:close-modal.window="close()"
     x-on:open-modal="open()"
     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
>
    <div class="modal {{$class ?? ''}}" style="{{ $style ?? '' }}" x-ref="modal"
         x-on:click.outside="if (! $event.target.closest('#tiptap-link-modal') || $el.closest('#tiptap-link-modal')) { close(); }">
        <div class="modal-header">
            <i class="icon-sm icon-info mr-3"></i>
            <h6 class="heading-6">
                {{ $header ?? __('cooperation/frontend/shared.modals.info') }}
            </h6>
            <div class="modal-close" x-on:click="close()">
                <i class="icon-md icon-close-circle-light clickable"></i>
            </div>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
    </div>
</div>