<div class="flex items-center justify-between mb-3 p-3 bg-gray-50 rounded">
    <div>
        <span class="font-medium">{{ $scan->name }}</span>
        <span class="text-sm text-gray-500">
            (@lang('cooperation/admin/buildings.show.small-measures.cooperation-disabled'))
        </span>
    </div>

    <label class="flex items-center cursor-pointer">
        <input type="checkbox"
               wire:model.live="enabled"
               class="h-5 w-5">
        <span class="ml-2">
            @lang('cooperation/admin/buildings.show.small-measures.enable-for-building')
        </span>
    </label>
</div>
