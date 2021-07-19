<div x-data="alpineSelect({{$initiallyOpen ?? false}})" x-ref="select-wrapper" class="select-wrapper w-inherit">
    {{-- Expect at least a select with options --}}
    {{ $slot }}

    <div class="input-group">
        @if(! empty(($icon ?? null)))
            <i class="select-icon {{$icon}} absolute left-6 top-1/20 cursor-pointer" x-on:click="toggle"></i>
        @endif
        <input class="form-input select-input" readonly x-ref="select-input" x-model="value" x-on:click="toggle"
               style="display: none">
        <i x-show="open === false"
           class="icon-sm icon-arrow-down opacity-50" x-on:click="toggle"></i>
        <i x-show="open === true"
           class="icon-sm icon-arrow-up absolute" x-on:click="toggle"></i>
    </div>

    <div x-ref="select-options" class="select-dropdown" x-show="open">
        <!-- Will be populated by Alpine -->
    </div>
</div>