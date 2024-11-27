<div id="sidebar" class="w-full mb-4 xl:w-3/20 xl:mb-0">
    <div class="w-full border border-solid border-blue-500 border-opacity-50 rounded-lg" x-data="{open: true}">
        @if(! empty($breadcrumbsSlot))
            {{ $breadcrumbsSlot }}
        @endif

        <div class="flex items-center cursor-pointer p-2" x-on:click="open = !open">
            <h3 class="heading-5 inline-block mr-2 select-none">
                {{ $sidebarTitle }}
            </h3>
            <i x-show="open == false" class="icon-sm icon-arrow-down cursor-pointer select-none" x-on:click="toggle()"></i>
            <i x-cloak x-show="open == true" class="icon-sm icon-arrow-up cursor-pointer select-none" x-on:click="toggle()"></i>
        </div>

        {{-- List styling is done via resources/css/admin/components/items.css --}}
        <ul x-show="open">
            {{ $slot }}
        </ul>
    </div>
</div>