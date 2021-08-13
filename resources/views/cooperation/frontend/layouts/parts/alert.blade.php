<?php $color = $color ?? 'blue'; ?>

<div id="{{$id ?? ''}}" role="alert" x-data="{display: !!{{$display ?? true}}}" x-show="display"
     class="alert flex flex-row flex-wrap items-center w-full p-4 relative rounded-lg text-sm text-{{$color}} border border-solid border-{{$color}} my-3 @if(($withBackground ?? false)) bg-{{$color}} bg-opacity-25 @else bg-white @endif {{$class ?? ''}}">
    @if(($dismissible ?? true))
        <div class="flex items-center absolute inset-y-0 right-3 {{$closeClass ?? ''}}" x-on:click="display = false;">
            <i class="icon-md icon-close-circle-light clickable"></i>
        </div>
    @endif

    <div class="@if(($dismissible ?? true)) w-17/20 @else w-full @endif text-left">
        {{ $slot }}
    </div>
</div>
