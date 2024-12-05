@props([
    'open' => false,
    'arrow' => true,
    'position' => 'right',
    'height' => 0,
    'offset' => 8,
    'trigger' => ['hover', 'click'],
    'class' => 'inline-flex items-center',
    'style' => null,
    //'self' => false,
    'id' => null,
])

<span x-data="popover(@js($open), @js($arrow), @js($position), @js($height), @js($offset), {{json_encode(Arr::wrap($trigger))}})"
    x-on:resize.window="updatePosition"
{{--    @if($self) x-bind="popover" @endif--}}
    x-bind="popover"
    @if(! empty($id)) id="{{$id}}" @endif
    @if(! empty($class)) class="{{$class}}" @endif
    @if(! empty($style)) style="{{$style}}" @endif
>
    {{ $slot }}
    <div x-ref="body"
         x-show="open"
         x-cloak
         role="tooltip"
         x-bind:class="{
            'popover-top': position == 'top',
            'popover-bottom': position == 'bottom',
            'popover-left': position == 'left',
            'popover-right': position == 'right',
            'show': open,
         }"
         class="popover align-items-center"
    >
        <div x-show="arrow" class="arrow"></div>
        <div x-ref="inner" x-show="open" class="popover-body">
            {!! $body !!}
        </div>
    </div>
</span>
