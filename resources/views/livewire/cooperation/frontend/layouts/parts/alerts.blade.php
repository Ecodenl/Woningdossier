<div x-data="dropdown()" x-ref="dropdown-wrapper"
     class="dropdown-wrapper w-inherit flex items-center">
    <input type="hidden" wire:model="alertOpen"
           x-on:element:updated.window="if ($event.detail.field === 'alertOpen') { toggle($event.detail.value); }">
    <a href="#" x-on:click="toggle()" x-ref="dropdown-toggle" class="dropdown-toggle select-none flex mr-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="text-white h-7 w-7 bg-blue-500 rounded-full p-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <div class="absolute flex-shrink-0 flex -right-1 -top-1">
            <span class="h-3 w-3 rounded-full @if($alerts->isNotEmpty()) border border-white bg-red @endif" aria-hidden="true"></span>
        </div>
    </a>
    <ul x-cloak x-show="open" x-ref="dropdown" class="dropdown w-96" x-on:click.outside="close()" wire:ignore.self>
        @if($alerts->isEmpty())
            <li>
                <p class="text-green text-sm">
                    @lang('cooperation/frontend/layouts.navbar.alerts.empty')
                </p>
            </li>
        @else
            @foreach($alerts as $alert)
                <li>
                    <p class="{{ $typeMap[$alert['type']] }} text-sm">
                        {{$alert->text}}
                    </p>
                </li>
            @endforeach
        @endif
    </ul>
</div>