<div class="flex items-center justify-between mb-3 p-3 bg-gray-50 rounded">
    <div>
        <span class="font-medium">{{ $scan->name }}</span>
    </div>

    <div class="flex items-center">
        <label class="flex items-center {{ $canToggle ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}">
            <input type="checkbox"
                   wire:model.live="enabled"
                   class="h-5 w-5"
                   @if(! $canToggle) disabled @endif>
            <span class="ml-2">
                @lang('cooperation/admin/buildings.show.scan-availability.enable-for-building')
            </span>
        </label>

        @if(! $canToggle && $disabledReason)
            <span class="relative inline-flex items-center ml-2"
                  x-data="{ show: false }"
                  x-on:mouseenter="show = true"
                  x-on:mouseleave="show = false">
                <i class="icon-sm icon-info-light clickable"></i>
                <div x-show="show" x-cloak
                     class="popover popover-left show"
                     style="right: calc(100% + 0.5rem); top: 50%; transform: translateY(-50%);">
                    <div class="arrow"></div>
                    <div class="popover-body">
                        <p>{{ $disabledReason }}</p>
                    </div>
                </div>
            </span>
        @endif
    </div>
</div>
