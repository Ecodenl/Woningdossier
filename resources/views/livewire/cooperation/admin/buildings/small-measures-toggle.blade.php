<div class="flex items-center justify-between mb-3 p-3 bg-gray-50 rounded">
    <div>
        <span class="font-medium">{{ $scan->name }}</span>
        <span class="text-sm text-gray-500">
            @if($locked)
                (@lang('cooperation/admin/buildings.show.small-measures.always-enabled'))
            @else
                (@lang($cooperationEnabled
                    ? 'cooperation/admin/buildings.show.small-measures.cooperation-enabled'
                    : 'cooperation/admin/buildings.show.small-measures.cooperation-disabled'))
            @endif
        </span>
    </div>

    <label class="flex items-center {{ $locked ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
        <input type="checkbox"
               wire:model.live="enabled"
               class="h-5 w-5"
               @disabled($locked)>
        <span class="ml-2">
            @lang('cooperation/admin/buildings.show.small-measures.toggle-for-building')
        </span>
    </label>
</div>
