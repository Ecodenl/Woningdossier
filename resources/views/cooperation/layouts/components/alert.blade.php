@php
    // Callers pass a colour; map it to an alert type. Unknown colours fall back to info.
    $type = $type ?? match ($color ?? 'blue') {
        'green' => 'success',
        'yellow', 'orange' => 'warning',
        'red' => 'error',
        default => 'info',
    };

    // Full class names, so Tailwind picks them up without a safelist.
    $typeClasses = match ($type) {
        'success' => 'border-alert-success-border bg-alert-success-bg',
        'warning' => 'border-alert-warning-border bg-alert-warning-bg',
        'error' => 'border-alert-error-border bg-alert-error-bg',
        default => 'border-alert-info-border bg-alert-info-bg',
    };
@endphp

<div @if(! empty($id)) id="{{$id}}" @endif role="alert" x-data="{display: @js($display ?? true)}" x-show="display" x-cloak
     class="alert alert-{{$type}} flex flex-row flex-wrap items-center w-full p-4 relative rounded-lg text-sm text-blue border border-solid {{$typeClasses}} my-3 {{$class ?? ''}}"
     @if(! empty($attr)) {!! $attr !!} @endif>
    @if(($dismissible ?? true))
        <div class="flex items-center absolute inset-y-0 right-3 {{$closeClass ?? ''}}" x-on:click="display = false;">
            <i class="icon-md icon-close-circle-light clickable"></i>
        </div>
    @endif

    <div class="@if(($dismissible ?? true)) w-17/20 @else w-full @endif text-left">
        {{ $slot }}
    </div>
</div>
