<div x-data="alpineSelect" x-ref="select-wrapper" class="w-inherit">
    {{-- Expect at least a select with options --}}
    {{ $slot }}

    <div class="input-group">
        <input class="form-input select-input" readonly x-ref="select-input" x-model="value" x-on:click="toggle"
               style="display: none">
        <i x-show="open === false"
           class="icon-md icon-arrow-down opacity-50 absolute right-6 top-3/20"></i>
        <i x-show="open === true"
           class="icon-md icon-arrow-up absolute right-6 top-3/20"></i>
    </div>

    <div x-ref="select-options" class="select-dropdown" x-show="open">
        <!-- Will be populated by Alpine -->
    </div>
</div>