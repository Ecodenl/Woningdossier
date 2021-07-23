<?php $color = $color ?? 'blue'; ?>

<div id="{{$id ?? ''}}" class="w-full p-4 relative bg-white rounded-lg text-sm text-{{$color}} border border-solid border-{{$color}} my-3 {{$class ?? ''}}" role="alert" x-data="{display: true}" x-show="display">
    @if(($dismissible ?? true))
        <div class="absolute right-3 top-3 cursor-pointer" x-on:click="display = false;">
            <i class="icon-md icon-close-circle-light"></i>
        </div>
    @endif

    <div class="@if(($dismissible ?? true)) w-3/4 @else w-full @endif">
        {{ $slot }}
    </div>
</div>
