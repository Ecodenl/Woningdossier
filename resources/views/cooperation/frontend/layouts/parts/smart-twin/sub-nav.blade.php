{{-- $items is assembled by the SmartTwinSubNavComposer, bound to this view in the ViewServiceProvider. --}}
<div class="flex flex-row flex-wrap items-center w-full bg-gray-500 px-5 xl:px-20 h-14 space-x-8 xl:space-x-12 relative z-130">
    @foreach($items as $item)
        <a href="{{ $item['url'] }}" class="sub-nav-item is-{{ $item['state'] }}">
            {{ $item['label'] }}
        </a>
    @endforeach
</div>
